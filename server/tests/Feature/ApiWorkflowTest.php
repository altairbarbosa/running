<?php

namespace Tests\Feature;

use App\Models\Charge;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_log_in_through_api(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->withHeader('referer', 'http://localhost:5174')
            ->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ])
            ->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonPath('redirect_to', '/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_api_permission_middleware_returns_json_forbidden(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($member)
            ->getJson('/api/finance')
            ->assertForbidden()
            ->assertJsonStructure(['message']);
    }

    public function test_admin_can_read_dashboard_and_create_member_through_api(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonStructure(['stats', 'workouts']);

        $this->actingAs($admin)
            ->postJson('/api/members', [
                'name' => 'Aluno API',
                'email' => 'aluno-api@running.test',
                'phone' => '(53) 98888-1111',
                'birth_date' => '1998-01-01',
                'address' => 'Rua API',
                'active' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('member.email', 'aluno-api@running.test');

        $this->assertDatabaseHas('users', ['email' => 'aluno-api@running.test', 'role' => 'member']);
    }

    public function test_member_can_read_own_billing_through_api(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $plan = Plan::create(['name' => 'Mensal API', 'price' => 120, 'billing_interval_months' => 1]);
        $membership = Membership::create(['member_id' => $member->id, 'plan_id' => $plan->id, 'starts_at' => today(), 'status' => 'active', 'billing_day' => 10, 'price' => 120]);
        Charge::create(['membership_id' => $membership->id, 'type' => 'monthly', 'description' => 'Mensalidade API', 'due_date' => today(), 'amount' => 120, 'status' => 'pending']);

        $this->actingAs($member)
            ->getJson('/api/my-billing')
            ->assertOk()
            ->assertJsonPath('memberships.0.plan.name', 'Mensal API')
            ->assertJsonPath('memberships.0.charges.0.description', 'Mensalidade API');
    }
}
