<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\Exercise;
use App\Models\Membership;
use App\Models\MuscleGroup;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PermissionGroup;
use App\Models\Plan;
use App\Models\Product;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutTemplate;
use App\Notifications\TemporaryPasswordNotification;
use App\Rules\PhoneNumber;
use App\Services\AvatarService;
use App\Services\BillingService;
use App\Services\ExerciseMediaService;
use App\Services\TemporaryPasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;

class RunningApiController extends Controller
{
    public function dashboard(Request $request, BillingService $billing)
    {
        $user = $request->user();
        $managesWorkouts = $user->hasPermission('workouts.manage');

        if ($user->hasPermission('billing.manage')) {
            $billing->refreshOverdueStatuses();
        }

        return response()->json([
            'stats' => [
                'member_count' => $user->hasPermission('members.view') ? User::where('role', 'member')->count() : null,
                'exercise_count' => $user->hasPermission('exercises.manage') ? Exercise::where('active', true)->count() : null,
                'workout_count' => $managesWorkouts ? Workout::where('status', 'active')->count() : $user->workouts()->where('status', 'active')->count(),
                'membership_count' => $user->hasPermission('memberships.manage') ? Membership::where('status', 'active')->count() : null,
                'overdue_amount' => $user->hasPermission('billing.manage') ? $this->openCharges()->where('status', 'overdue')->sum(DB::raw($this->openExpression())) : null,
                'received_this_month' => $user->hasPermission('billing.manage') ? Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount') : null,
            ],
            'workouts' => Workout::with(['member', 'items'])
                ->when(! $managesWorkouts, fn ($query) => $query->where('member_id', $user->id))
                ->latest('starts_at')
                ->limit(5)
                ->get(),
        ]);
    }

    public function members(Request $request)
    {
        return response()->json([
            'members' => User::with($this->memberRelations())
                ->where('role', 'member')
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = '%'.$request->string('search')->trim().'%';
                    $query->where(fn ($member) => $member->where('name', 'like', $search)->orWhere('email', 'like', $search));
                })
                ->orderBy('name')
                ->paginate((int) $request->integer('per_page', 12)),
        ]);
    }

    public function storeMember(Request $request, TemporaryPasswordService $passwords)
    {
        $data = $this->memberData($request);
        $temporaryPassword = $passwords->generate();
        $user = User::create([
            ...$data,
            'role' => 'member',
            'password' => $temporaryPassword,
            'must_change_password' => true,
            'permission_group_id' => PermissionGroup::where('key', 'member')->value('id'),
        ]);
        $user->notify(new TemporaryPasswordNotification($temporaryPassword));

        return response()->json(['message' => 'Aluno cadastrado. A senha temporária foi enviada por e-mail.', 'member' => $user], 201);
    }

    public function updateMember(Request $request, User $member)
    {
        abort_unless($member->role === 'member', 404);
        $member->update($this->memberData($request, $member));

        return response()->json(['message' => 'Cadastro atualizado.', 'member' => $member->fresh()]);
    }

    public function destroyMember(User $member)
    {
        abort_unless($member->role === 'member', 404);
        $member->update(['active' => false]);

        return response()->json(['message' => 'Aluno desativado.']);
    }

    public function users(Request $request)
    {
        return response()->json([
            'users' => User::with('permissionGroup')
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = '%'.$request->string('search')->trim().'%';
                    $query->where(fn ($user) => $user->where('name', 'like', $search)->orWhere('email', 'like', $search));
                })
                ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
                ->orderBy('name')
                ->paginate((int) $request->integer('per_page', 15)),
            'permission_groups' => PermissionGroup::orderByDesc('is_system')->orderBy('name')->get(),
        ]);
    }

    public function storeUser(Request $request, AvatarService $avatars, TemporaryPasswordService $passwords)
    {
        $data = $this->userData($request);
        $temporaryPassword = $passwords->generate();
        $data['password'] = $temporaryPassword;
        $data['must_change_password'] = true;
        $data['permission_group_id'] ??= PermissionGroup::where('key', $data['role'])->value('id');
        $user = User::create(Arr::except($data, ['avatar', 'remove_avatar']));
        $avatars->update($user, $request->file('avatar'));
        $user->notify(new TemporaryPasswordNotification($temporaryPassword));

        return response()->json(['message' => 'Usuário criado. A senha temporária foi enviada por e-mail.', 'user' => $user->fresh()], 201);
    }

    public function updateUser(Request $request, User $user, AvatarService $avatars)
    {
        $data = $this->userData($request, $user);

        if ($request->user()->is($user)) {
            $data['role'] = $user->role;
            $data['active'] = true;
        }

        $user->update(Arr::except($data, ['avatar', 'remove_avatar']));
        $avatars->update($user, $request->file('avatar'), $request->boolean('remove_avatar'));

        return response()->json(['message' => 'Usuário atualizado.', 'user' => $user->fresh()]);
    }

    public function destroyUser(Request $request, User $user)
    {
        abort_if($request->user()->is($user), 422, 'Você não pode desativar sua própria conta.');
        $user->update(['active' => false]);

        return response()->json(['message' => 'Usuário desativado.']);
    }

    public function permissionGroups()
    {
        return response()->json([
            'groups' => PermissionGroup::withCount('users')->with('permissions')->orderByDesc('is_system')->orderBy('name')->get(),
            'catalog' => config('permissions.catalog'),
        ]);
    }

    public function storePermissionGroup(Request $request)
    {
        $data = $this->permissionGroupData($request);
        $key = Str::slug($data['name']);
        $base = $key ?: 'grupo';
        for ($suffix = 2; PermissionGroup::where('key', $key)->exists(); $suffix++) {
            $key = $base.'-'.$suffix;
        }
        $group = PermissionGroup::create(['name' => $data['name'], 'key' => $key, 'description' => $data['description'] ?? null]);
        $this->syncPermissions($group, $data['permissions'] ?? []);

        return response()->json(['message' => 'Grupo de permissões criado.', 'group' => $group->load('permissions')], 201);
    }

    public function updatePermissionGroup(Request $request, PermissionGroup $permissionGroup)
    {
        $data = $this->permissionGroupData($request);
        $permissionGroup->update(['name' => $data['name'], 'description' => $data['description'] ?? null]);
        $this->syncPermissions($permissionGroup, $data['permissions'] ?? []);

        return response()->json(['message' => 'Grupo de permissões atualizado.', 'group' => $permissionGroup->load('permissions')]);
    }

    public function destroyPermissionGroup(PermissionGroup $permissionGroup)
    {
        abort_if($permissionGroup->is_system, 422, 'Grupos padrão não podem ser excluídos.');
        abort_if($permissionGroup->users()->exists(), 422, 'Reatribua os usuários antes de excluir este grupo.');
        $permissionGroup->delete();

        return response()->json(['message' => 'Grupo de permissões excluído.']);
    }

    public function exercises(Request $request)
    {
        return response()->json([
            'exercises' => Exercise::with(['muscleGroup', 'media'])
                ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->trim().'%'))
                ->when($request->integer('muscle_group_id'), fn ($query, $groupId) => $query->where('muscle_group_id', $groupId))
                ->orderBy('muscle_group_id')->orderBy('name')->paginate((int) $request->integer('per_page', 30)),
            'muscle_groups' => MuscleGroup::withCount('exercises')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function storeExercise(Request $request, ExerciseMediaService $mediaService)
    {
        $data = $this->exerciseData($request);
        $exercise = Exercise::create(collect($data)->except(['images', 'video_url', 'remove_media'])->all());
        $mediaService->update($exercise, $request->file('images', []), $data['video_url'] ?? null);

        return response()->json(['message' => 'Exercício cadastrado.', 'exercise' => $exercise->load(['muscleGroup', 'media'])], 201);
    }

    public function updateExercise(Request $request, Exercise $exercise, ExerciseMediaService $mediaService)
    {
        $data = $this->exerciseData($request, $exercise);
        $exercise->update(collect($data)->except(['images', 'video_url', 'remove_media'])->all());
        $mediaService->update($exercise, $request->file('images', []), $data['video_url'] ?? null, $data['remove_media'] ?? []);

        return response()->json(['message' => 'Exercício atualizado.', 'exercise' => $exercise->load(['muscleGroup', 'media'])]);
    }

    public function destroyExercise(Exercise $exercise)
    {
        $exercise->update(['active' => false]);

        return response()->json(['message' => 'Exercício desativado.']);
    }

    public function storeMuscleGroup(Request $request)
    {
        $data = $this->muscleGroupData($request);
        $group = MuscleGroup::create([...$data, 'slug' => Str::slug($data['name']), 'sort_order' => ((int) MuscleGroup::max('sort_order')) + 10]);

        return response()->json(['message' => 'Grupo muscular cadastrado.', 'group' => $group], 201);
    }

    public function updateMuscleGroup(Request $request, MuscleGroup $muscleGroup)
    {
        $data = $this->muscleGroupData($request, $muscleGroup);
        if (! $data['active'] && $muscleGroup->exercises()->exists()) {
            throw ValidationException::withMessages(['active' => 'Este grupo possui exercícios vinculados e não pode ser desativado.']);
        }
        $muscleGroup->update([...$data, 'slug' => Str::slug($data['name'])]);

        return response()->json(['message' => 'Grupo muscular atualizado.', 'group' => $muscleGroup->fresh()]);
    }

    public function reorderMuscleGroups(Request $request)
    {
        $data = $request->validate([
            'groups' => ['required', 'array'],
            'groups.*' => ['required', 'integer', 'distinct', 'exists:muscle_groups,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['groups'] as $index => $groupId) {
                MuscleGroup::whereKey($groupId)->update(['sort_order' => ($index + 1) * 10]);
            }
        });

        return response()->json(['saved' => true]);
    }

    public function workouts(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'workouts' => Workout::with(['member', 'items.exercise'])
                ->when(! $user->hasPermission('workouts.manage'), fn ($query) => $query->where('member_id', $user->id))
                ->when($user->hasPermission('workouts.manage') && $request->integer('member_id'), fn ($query) => $query->where('member_id', $request->integer('member_id')))
                ->latest('starts_at')
                ->paginate((int) $request->integer('per_page', 12)),
            'members' => $user->hasPermission('workouts.manage') ? $this->activeMembers() : [],
            'templates' => $user->hasPermission('workouts.manage') ? WorkoutTemplate::with(['items.exercise', 'author'])->where('active', true)->orderBy('name')->get() : [],
        ]);
    }

    public function workoutForm()
    {
        return response()->json([
            'members' => $this->activeMembers(),
            'exercises' => Exercise::with('muscleGroup')->where('active', true)->orderBy('muscle_group_id')->orderBy('name')->get(),
            'templates' => WorkoutTemplate::with(['items.exercise'])->where('active', true)->orderBy('name')->get(),
            'reusable_workouts' => Workout::with(['items.exercise', 'member'])->whereHas('items')->latest('starts_at')->limit(100)->get(),
        ]);
    }

    public function storeWorkout(Request $request)
    {
        $data = $this->workoutData($request);

        $workout = DB::transaction(function () use ($data, $request) {
            $workout = Workout::create([
                ...collect($data)->except('items')->all(),
                'created_by' => $request->user()->id,
                'status' => 'active',
            ]);

            foreach (array_values($data['items']) as $index => $item) {
                $workout->items()->create([...$item, 'position' => $index + 1]);
            }

            return $workout;
        });

        return response()->json(['message' => 'Treino elaborado com sucesso.', 'workout' => $workout->load(['member', 'items.exercise'])], 201);
    }

    public function workout(Request $request, Workout $workout)
    {
        abort_unless($request->user()->hasPermission('workouts.manage') || $workout->member_id === $request->user()->id, 403);

        return response()->json(['workout' => $workout->load(['member', 'author', 'items.exercise.media', 'items.exercise.muscleGroup'])]);
    }

    public function destroyWorkout(Workout $workout)
    {
        $workout->delete();

        return response()->json(['message' => 'Treino removido.']);
    }

    public function storeWorkoutTemplate(Request $request, Workout $workout)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:1000']]);

        $template = DB::transaction(function () use ($data, $request, $workout) {
            $template = WorkoutTemplate::create([...$data, 'created_by' => $request->user()->id, 'active' => true]);
            $template->items()->createMany($workout->items->map(fn ($item) => [
                'exercise_id' => $item->exercise_id,
                'position' => $item->position,
                'sets' => $item->sets,
                'repetitions' => $item->repetitions,
                'rest_seconds' => $item->rest_seconds,
            ])->all());

            return $template;
        });

        return response()->json(['message' => 'Modelo criado. Agora ele pode ser atribuído a outros alunos.', 'template' => $template->load('items.exercise')], 201);
    }

    public function destroyWorkoutTemplate(WorkoutTemplate $workoutTemplate)
    {
        $workoutTemplate->update(['active' => false]);

        return response()->json(['message' => 'Modelo arquivado.']);
    }

    public function shop(Request $request)
    {
        return response()->json([
            'products' => Product::when(! $request->user()->hasPermission('shop.manage'), fn ($query) => $query->where('active', true)->where('stock', '>', 0))->orderBy('name')->get(),
            'orders' => $request->user()->hasPermission('shop.manage')
                ? Order::with(['member', 'items'])->latest('ordered_at')->limit(20)->get()
                : Order::with('items')->where('member_id', $request->user()->id)->latest('ordered_at')->get(),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $data = $this->productData($request);
        $path = $request->file('image')?->store('products', 'public');
        $product = Product::create([...$data, 'image_path' => $path]);

        return response()->json(['message' => 'Produto cadastrado.', 'product' => $product], 201);
    }

    public function updateProduct(Request $request, Product $product)
    {
        $data = $this->productData($request);
        if ($request->file('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);

        return response()->json(['message' => 'Produto atualizado.', 'product' => $product->fresh()]);
    }

    public function orderProduct(Request $request, Product $product)
    {
        abort_unless($request->user()->role === 'member', 403);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:20']]);

        DB::transaction(function () use ($request, $product, $data) {
            $product = Product::lockForUpdate()->findOrFail($product->id);
            abort_unless($product->active && $product->stock >= $data['quantity'], 422, 'Estoque insuficiente.');
            $subtotal = (float) $product->price * $data['quantity'];
            $order = Order::create(['member_id' => $request->user()->id, 'status' => 'pending', 'total' => $subtotal, 'ordered_at' => now()]);
            $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => $product->price, 'quantity' => $data['quantity'], 'subtotal' => $subtotal]);
            $product->decrement('stock', $data['quantity']);
        });

        return response()->json(['message' => 'Pedido realizado. A academia fará a confirmação.']);
    }

    public function plans()
    {
        return response()->json([
            'plans' => Plan::withCount(['memberships' => fn ($query) => $query->where('status', 'active')])->orderBy('name')->get(),
        ]);
    }

    public function storePlan(Request $request)
    {
        $plan = Plan::create($this->planData($request));

        return response()->json(['message' => 'Plano cadastrado.', 'plan' => $plan], 201);
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $plan->update($this->planData($request, $plan));

        return response()->json(['message' => 'Plano atualizado. As matrículas existentes mantiveram seus valores.', 'plan' => $plan->fresh()]);
    }

    public function destroyPlan(Plan $plan)
    {
        $plan->update(['active' => false]);

        return response()->json(['message' => 'Plano desativado.']);
    }

    public function memberships(Request $request)
    {
        return response()->json([
            'memberships' => Membership::with(['member', 'plan'])
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->whereHas('member', fn ($member) => $member->where('name', 'like', '%'.$request->string('search')->trim().'%'));
                })
                ->latest()
                ->paginate((int) $request->integer('per_page', 15)),
            'members' => $this->activeMembers(),
            'plans' => Plan::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeMembership(Request $request, BillingService $billing)
    {
        $data = $this->membershipData($request);

        if (Membership::where('member_id', $data['member_id'])->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['member_id' => 'Este aluno já possui uma matrícula ativa.']);
        }

        $membership = DB::transaction(function () use ($data, $billing) {
            $plan = Plan::findOrFail($data['plan_id']);
            $membership = Membership::create([...$data, 'price' => $plan->price, 'status' => 'active']);
            $billing->generateCharges($membership);

            return $membership;
        });

        return response()->json(['message' => 'Matrícula criada e cobranças geradas.', 'membership' => $membership->load(['member', 'plan', 'charges'])], 201);
    }

    public function membership(Membership $membership, BillingService $billing)
    {
        $billing->refreshOverdueStatuses();

        return response()->json(['membership' => $membership->load(['member', 'plan', 'charges.payments.receiver'])]);
    }

    public function cancelMembership(Membership $membership)
    {
        DB::transaction(function () use ($membership) {
            $membership->update(['status' => 'cancelled', 'ends_at' => $membership->ends_at ?? today()]);
            $membership->charges()->whereIn('status', ['pending', 'partial'])->whereDate('due_date', '>=', today())->update(['status' => 'cancelled']);
        });

        return response()->json(['message' => 'Matrícula cancelada; cobranças futuras foram canceladas.']);
    }

    public function finance(Request $request, BillingService $billing)
    {
        $billing->refreshOverdueStatuses();

        return response()->json([
            'charges' => Charge::with(['membership.member', 'membership.plan'])
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->whereHas('membership.member', fn ($member) => $member->where('name', 'like', '%'.$request->string('search')->trim().'%'));
                })
                ->orderByDesc('due_date')
                ->paginate((int) $request->integer('per_page', 20)),
            'summary' => [
                'open_amount' => $this->openCharges()->sum(DB::raw($this->openExpression())),
                'overdue_amount' => Charge::where('status', 'overdue')->sum(DB::raw($this->openExpression())),
                'received_this_month' => Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            ],
        ]);
    }

    public function storePayment(Request $request, Charge $charge, BillingService $billing)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', Rule::in(['cash', 'pix', 'credit_card', 'debit_card', 'bank_transfer'])],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $payment = $billing->registerPayment($charge, $data, $request->user()->id);

        return response()->json(['message' => 'Pagamento registrado com sucesso.', 'payment' => $payment->load('receiver')], 201);
    }

    public function ownBilling(Request $request, BillingService $billing)
    {
        abort_unless($request->user()->role === 'member', 403);
        $billing->refreshOverdueStatuses();

        $memberships = $request->user()
            ->memberships()
            ->with(['plan', 'charges.payments'])
            ->latest()
            ->get();

        $openAmount = Charge::whereHas('membership', fn ($query) => $query->where('member_id', $request->user()->id))
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->get()
            ->sum(fn ($charge) => (float) $charge->outstanding);

        return response()->json(['memberships' => $memberships, 'open_amount' => $openAmount]);
    }

    private function memberData(Request $request, ?User $member = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($member)],
            'phone' => ['nullable', 'string', 'max:30', new PhoneNumber],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['required', 'boolean'],
        ]);
    }

    private function userData(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(['admin', 'trainer', 'member'])],
            'permission_group_id' => ['nullable', 'exists:permission_groups,id'],
            'phone' => ['nullable', 'string', 'max:30', new PhoneNumber],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['required', 'boolean'],
            'avatar' => ['nullable', File::image()->max(2 * 1024)],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);
    }

    private function permissionGroupData(Request $request): array
    {
        $allowed = collect(config('permissions.catalog'))->flatMap(fn ($items) => array_keys($items))->all();

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allowed)],
        ]);
    }

    private function syncPermissions(PermissionGroup $group, array $permissions): void
    {
        $group->permissions()->delete();
        $group->permissions()->createMany(array_map(fn ($permission) => ['permission' => $permission], array_values(array_unique($permissions))));
    }

    private function exerciseData(Request $request, ?Exercise $exercise = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('exercises')->ignore($exercise)],
            'muscle_group_id' => ['required', 'integer', 'exists:muscle_groups,id'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'active' => ['required', 'boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['required', File::image()->max(5 * 1024)],
            'video_url' => ['nullable', 'url:http,https', 'max:2048'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['integer', 'exists:exercise_media,id'],
        ]);
    }

    private function muscleGroupData(Request $request, ?MuscleGroup $group = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('muscle_groups')->ignore($group)],
            'active' => ['required', 'boolean'],
        ]);
    }

    private function workoutData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'member')->where('active', true))],
            'name' => ['required', 'string', 'max:120'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'workout_template_id' => ['nullable', 'exists:workout_templates,id'],
            'source_workout_id' => ['nullable', 'exists:workouts,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.exercise_id' => ['required', 'distinct', 'exists:exercises,id'],
            'items.*.sets' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.repetitions' => ['required', 'string', 'max:30'],
            'items.*.weight' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'items.*.rest_seconds' => ['nullable', 'integer', 'min:0', 'max:3600'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function productData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'stock' => ['required', 'integer', 'min:0'],
            'active' => ['required', 'boolean'],
            'image' => ['nullable', File::image()->max(3 * 1024)],
        ], [
            'image.uploaded' => 'Não foi possível enviar a imagem. Use um arquivo de até 3 MB.',
            'image.image' => 'A imagem deve ser um arquivo JPG, PNG, GIF, BMP ou WebP.',
            'image.max' => 'A imagem deve ter no máximo 3 MB.',
        ], ['image' => 'imagem']);
    }

    private function planData(Request $request, ?Plan $plan = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('plans')->ignore($plan)],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'enrollment_fee' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'billing_interval_months' => ['required', 'integer', Rule::in([1, 3, 6, 12])],
            'description' => ['nullable', 'string', 'max:2000'],
            'active' => ['required', 'boolean'],
        ]);
    }

    private function membershipData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'member')->where('active', true))],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('active', true)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'billing_day' => ['required', 'integer', 'between:1,28'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function memberRelations(): array
    {
        return [
            'workouts.items',
            'memberships.plan',
            'memberships.charges',
            'orders' => fn ($query) => $query->with('items')->latest('ordered_at')->limit(10),
        ];
    }

    private function activeMembers()
    {
        return User::where('role', 'member')->where('active', true)->orderBy('name')->get();
    }

    private function openCharges()
    {
        return Charge::whereIn('status', ['pending', 'partial', 'overdue']);
    }

    private function openExpression(): string
    {
        return 'amount + late_fee - discount - paid_amount';
    }
}
