<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PermissionGroup;
use App\Notifications\TemporaryPasswordNotification;
use App\Rules\PhoneNumber;
use App\Services\TemporaryPasswordService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = User::query()->with($this->memberRelations())
            ->where('role', 'member')
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $search = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $modalMember = $request->integer('member') ? User::with($this->memberRelations())->where('role', 'member')->find($request->integer('member')) : null;

        return view('members.index', compact('members', 'modalMember'));
    }

    public function create()
    {
        return redirect()->route('members.index', ['modal' => 'create']);
    }

    public function store(Request $request, TemporaryPasswordService $passwords)
    {
        $data = $this->validated($request);
        $data['role'] = 'member';
        $temporaryPassword = $passwords->generate();
        $data['password'] = $temporaryPassword;
        $data['must_change_password'] = true;
        $data['permission_group_id'] = PermissionGroup::where('key', 'member')->value('id');
        $user = User::create($data);
        $user->notify(new TemporaryPasswordNotification($temporaryPassword));

        return redirect()->route('members.index')->with('success', 'Aluno cadastrado. A senha temporária foi enviada por e-mail.');
    }

    public function edit(User $member)
    {
        abort_unless($member->role === 'member', 404);

        return redirect()->route('members.index', ['modal' => 'edit', 'member' => $member->id]);
    }

    public function update(Request $request, User $member)
    {
        abort_unless($member->role === 'member', 404);
        $data = $this->validated($request, $member);
        $member->update($data);

        return redirect()->route('members.index')->with('success', 'Cadastro atualizado.');
    }

    public function destroy(User $member)
    {
        abort_unless($member->role === 'member', 404);
        $member->update(['active' => false]);

        return back()->with('success', 'Aluno desativado.');
    }

    private function validated(Request $request, ?User $member = null): array
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

    private function memberRelations(): array
    {
        return [
            'workouts.items',
            'memberships.plan',
            'memberships.charges',
            'orders' => fn ($query) => $query->with('items')->latest('ordered_at')->limit(10),
        ];
    }
}
