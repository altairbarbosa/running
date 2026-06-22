<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\MuscleGroup;
use App\Models\Product;
use App\Models\Membership;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RunningWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_log_in(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'password', 'active' => false]);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_staff_can_create_a_workout_with_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);
        $exercise = Exercise::create(['name' => 'Supino reto', 'active' => true]);

        $response = $this->actingAs($admin)->post(route('workouts.store'), [
            'member_id' => $member->id,
            'name' => 'Treino A',
            'starts_at' => '2026-06-21',
            'items' => [[
                'exercise_id' => $exercise->id,
                'sets' => 4,
                'repetitions' => '8-12',
                'weight' => 42.5,
                'rest_seconds' => 90,
            ]],
        ]);

        $workout = Workout::firstOrFail();
        $response->assertRedirect(route('workouts.show', $workout));
        $this->assertDatabaseHas('workout_items', ['workout_id' => $workout->id, 'position' => 1]);
    }

    public function test_workout_can_be_reused_as_template_with_individual_loads(): void
    {
        $admin=User::factory()->create(['role'=>'admin']); $first=User::factory()->create(['role'=>'member']); $second=User::factory()->create(['role'=>'member']);
        $exercise=Exercise::create(['name'=>'Agachamento modelo','active'=>true]);
        $workout=Workout::create(['member_id'=>$first->id,'created_by'=>$admin->id,'name'=>'Treino base','starts_at'=>today(),'status'=>'active']);
        $workout->items()->create(['exercise_id'=>$exercise->id,'position'=>1,'sets'=>4,'repetitions'=>'10','weight'=>80,'rest_seconds'=>90,'notes'=>'Carga individual']);

        $this->actingAs($admin)->get(route('workouts.create'))->assertOk()->assertSee('Treino base')->assertSee($first->name);

        $this->post(route('workout-templates.store-from-workout',$workout),['name'=>'Modelo pernas'])->assertRedirect();
        $template=WorkoutTemplate::with('items')->firstOrFail();
        $this->assertSame('Agachamento modelo',$template->items->first()->exercise->name);
        $this->get(route('workouts.index'))->assertOk()->assertSee('Treinos atribuídos')->assertSee('Modelos');
        $this->get(route('members.index',['modal'=>'edit','member'=>$first->id]))->assertOk()->assertSee('Treino base')->assertSee('Clonar para este aluno');

        $this->post(route('workouts.store'),['member_id'=>$second->id,'workout_template_id'=>$template->id,'name'=>'Treino personalizado','starts_at'=>today()->toDateString(),'items'=>[['exercise_id'=>$exercise->id,'sets'=>4,'repetitions'=>'10','weight'=>55,'rest_seconds'=>90,'notes'=>'Carga da segunda aluna']]])->assertRedirect();
        $assigned=Workout::where('member_id',$second->id)->firstOrFail();
        $this->assertSame('55.00',$assigned->items->first()->weight);
        $this->assertSame($template->id,$assigned->workout_template_id);
    }

    public function test_staff_can_create_and_filter_exercises_by_muscle_group(): void
    {
        $staff = User::factory()->create(['role' => 'trainer']);
        $group = MuscleGroup::where('name', 'Peitoral')->firstOrFail();

        $this->actingAs($staff)->get(route('exercises.create'))
            ->assertRedirect(route('exercises.index', ['modal' => 'create']));

        $this->actingAs($staff)->post(route('exercises.store'), [
            'name' => 'Crucifixo inclinado',
            'muscle_group_id' => $group->id,
            'instructions' => 'Controlar a fase excêntrica.',
            'active' => true,
        ])->assertRedirect(route('exercises.index'));

        $this->assertDatabaseHas('exercises', [
            'name' => 'Crucifixo inclinado',
            'muscle_group_id' => $group->id,
        ]);

        $this->actingAs($staff)->get(route('exercises.index', ['muscle_group_id' => $group->id]))
            ->assertOk()
            ->assertSee('Peitoral')
            ->assertSee('Crucifixo inclinado');
    }

    public function test_exercise_accepts_images_and_external_video_urls(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['role' => 'trainer']);
        $group = MuscleGroup::where('name', 'Peitoral')->firstOrFail();
        $image = UploadedFile::fake()->createWithContent(
            'supino.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
        );

        $this->actingAs($staff)->post(route('exercises.store'), [
            'name' => 'Supino com mídia',
            'muscle_group_id' => $group->id,
            'active' => true,
            'images' => [$image],
            'video_url' => 'https://www.youtube.com/watch?v=abc123',
        ])->assertRedirect(route('exercises.index'));

        $exercise = Exercise::where('name', 'Supino com mídia')->firstOrFail();
        $this->assertCount(2, $exercise->media);
        $this->assertSame('youtube', $exercise->media->firstWhere('type', 'video')->provider);
        Storage::disk('public')->assertExists($exercise->media->firstWhere('type', 'image')->path);
    }

    public function test_staff_can_manage_muscle_groups_without_orphaning_exercises(): void
    {
        $staff = User::factory()->create(['role' => 'trainer']);

        $this->actingAs($staff)->post(route('muscle-groups.store'), [
            'name' => 'Mobilidade', 'sort_order' => 140, 'active' => true,
        ])->assertRedirect(route('exercises.index'));

        $group = MuscleGroup::where('name', 'Mobilidade')->firstOrFail();
        $this->put(route('muscle-groups.update', $group), [
            'name' => 'Mobilidade e alongamento', 'sort_order' => 145, 'active' => true,
        ])->assertRedirect(route('exercises.index'));

        $group->refresh();
        $this->assertSame('mobilidade-e-alongamento', $group->slug);
        $orderedIds = MuscleGroup::orderBy('sort_order')->pluck('id')->reverse()->values()->all();
        $this->patchJson(route('muscle-groups.reorder'), ['groups' => $orderedIds])->assertOk();
        $this->assertSame($orderedIds, MuscleGroup::orderBy('sort_order')->pluck('id')->all());
        Exercise::create(['name' => 'Alongamento dinâmico', 'muscle_group_id' => $group->id, 'active' => true]);

        $this->put(route('muscle-groups.update', $group), [
            'name' => $group->name, 'sort_order' => 145, 'active' => false,
            'group_modal' => 'edit',
        ])->assertSessionHasErrors('active');
        $this->assertTrue($group->fresh()->active);
    }

    public function test_management_forms_open_from_their_index_modals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);
        $plan = \App\Models\Plan::create([
            'name' => 'Plano modal', 'price' => 90, 'enrollment_fee' => 10,
            'billing_interval_months' => 1, 'active' => true,
        ]);

        foreach (['members.index', 'plans.index', 'memberships.index', 'users.index'] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }

        $this->get(route('members.create'))->assertRedirect(route('members.index', ['modal' => 'create']));
        $this->get(route('members.edit', $member))->assertRedirect(route('members.index', ['modal' => 'edit', 'member' => $member->id]));
        $this->get(route('plans.edit', $plan))->assertRedirect(route('plans.index', ['modal' => 'edit', 'plan' => $plan->id]));
        $this->get(route('memberships.create'))->assertRedirect(route('memberships.index', ['modal' => 'create']));
        $this->get(route('users.edit', $admin))->assertRedirect(route('users.index', ['modal' => 'edit', 'user' => $admin->id]));
    }

    public function test_member_can_view_own_billing_and_order_shop_products(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $other = User::factory()->create(['role' => 'member']);
        $plan = \App\Models\Plan::create(['name'=>'Plano aluno','price'=>100,'enrollment_fee'=>0,'billing_interval_months'=>1,'active'=>true]);
        $membership = Membership::create(['member_id'=>$member->id,'plan_id'=>$plan->id,'starts_at'=>today(),'status'=>'active','billing_day'=>10,'price'=>100]);
        $membership->charges()->create(['type'=>'monthly','description'=>'Mensalidade visível','due_date'=>today()->addDay(),'amount'=>100,'status'=>'pending']);
        $otherMembership = Membership::create(['member_id'=>$other->id,'plan_id'=>$plan->id,'starts_at'=>today(),'status'=>'active','billing_day'=>10,'price'=>100]);
        $otherMembership->charges()->create(['type'=>'monthly','description'=>'Débito privado','due_date'=>today()->addDay(),'amount'=>100,'status'=>'pending']);

        $this->actingAs($member)->get(route('portal.billing'))->assertOk()->assertSee('Mensalidade visível')->assertDontSee('Débito privado');

        $product = Product::create(['name'=>'Camiseta Running','price'=>50,'stock'=>3,'active'=>true]);
        $this->get(route('shop.index'))->assertOk()->assertSee('Camiseta Running');
        $this->post(route('shop.order',$product),['quantity'=>2])->assertRedirect();
        $this->assertDatabaseHas('orders',['member_id'=>$member->id,'total'=>100]);
        $this->assertSame(1,$product->fresh()->stock);
    }

    public function test_staff_can_create_a_product_with_an_image_larger_than_two_megabytes(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $image = UploadedFile::fake()->createWithContent('camiseta.png', $png.str_repeat("\0", 2500 * 1024));

        $this->actingAs($admin)->get(route('shop.index'))
            ->assertOk()
            ->assertSee('até 3 MB');

        $this->post(route('shop.products.store'), [
            'name' => 'Camiseta com foto',
            'price' => 59.90,
            'stock' => 10,
            'active' => '1',
            'image' => $image,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $product = Product::where('name', 'Camiseta com foto')->firstOrFail();
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_member_cannot_access_staff_area_or_another_members_workout(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $other = User::factory()->create(['role' => 'member']);
        $admin = User::factory()->create(['role' => 'admin']);
        $workout = Workout::create([
            'member_id' => $other->id,
            'created_by' => $admin->id,
            'name' => 'Privado',
            'starts_at' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($member)->get(route('members.index'))->assertForbidden();
        $this->actingAs($member)->get(route('workouts.show', $workout))->assertForbidden();
    }
}
