<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
