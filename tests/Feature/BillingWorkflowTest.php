<?php

namespace Tests\Feature;

use App\Models\Charge;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_membership_generates_enrollment_and_monthly_charges_without_duplicates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);
        $plan = Plan::create(['name' => 'Mensal', 'price' => 100, 'enrollment_fee' => 30, 'billing_interval_months' => 1]);

        $response = $this->actingAs($admin)->post(route('memberships.store'), [
            'member_id' => $member->id,
            'plan_id' => $plan->id,
            'starts_at' => today()->format('Y-m-d'),
            'billing_day' => 10,
        ]);

        $membership = Membership::firstOrFail();
        $response->assertRedirect(route('memberships.show', $membership));
        $this->assertDatabaseHas('charges', ['membership_id' => $membership->id, 'type' => 'enrollment', 'amount' => 30]);
        $this->assertDatabaseHas('charges', ['membership_id' => $membership->id, 'type' => 'monthly', 'amount' => 100]);
        $this->actingAs($admin)->get(route('memberships.show', $membership))->assertOk()->assertSee('Mensalidade');
        $this->actingAs($admin)->get(route('finance.index'))->assertOk()->assertSee('A receber');
        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertSee('Inadimplência');

        $count = Charge::count();
        app(BillingService::class)->generateCharges($membership);
        $this->assertSame($count, Charge::count());
    }

    public function test_partial_and_full_payments_update_charge_balance(): void
    {
        [$admin, $charge] = $this->chargeFixture();

        $this->actingAs($admin)->post(route('payments.store', $charge), [
            'amount' => 40,
            'paid_at' => now()->format('Y-m-d H:i:s'),
            'method' => 'pix',
        ])->assertSessionHasNoErrors();

        $this->assertSame('partial', $charge->fresh()->status);
        $this->assertSame('60.00', $charge->fresh()->outstanding);

        $this->actingAs($admin)->post(route('payments.store', $charge), [
            'amount' => 60,
            'paid_at' => now()->format('Y-m-d H:i:s'),
            'method' => 'cash',
        ])->assertSessionHasNoErrors();

        $this->assertSame('paid', $charge->fresh()->status);
        $this->assertSame('0.00', $charge->fresh()->outstanding);
    }

    public function test_payment_cannot_exceed_outstanding_balance(): void
    {
        [$admin, $charge] = $this->chargeFixture();

        $this->actingAs($admin)->post(route('payments.store', $charge), [
            'amount' => 100.01,
            'paid_at' => now()->format('Y-m-d H:i:s'),
            'method' => 'pix',
        ])->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
        $this->assertSame('0.00', $charge->fresh()->paid_amount);
    }

    private function chargeFixture(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);
        $plan = Plan::create(['name' => 'Mensal', 'price' => 100, 'billing_interval_months' => 1]);
        $membership = Membership::create(['member_id' => $member->id, 'plan_id' => $plan->id, 'starts_at' => today(), 'status' => 'active', 'billing_day' => 10, 'price' => 100]);
        $charge = Charge::create(['membership_id' => $membership->id, 'type' => 'monthly', 'description' => 'Mensalidade', 'due_date' => today(), 'amount' => 100, 'status' => 'pending']);

        return [$admin, $charge];
    }
}
