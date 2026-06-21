<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\Plan;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $member = User::where('email', 'aluno@running.test')->firstOrFail();
        $plan = Plan::updateOrCreate(
            ['name' => 'Musculação mensal'],
            [
                'price' => 99.90,
                'enrollment_fee' => 50,
                'billing_interval_months' => 1,
                'description' => 'Acesso livre à sala de musculação.',
                'active' => true,
            ],
        );

        $membership = Membership::firstOrCreate(
            ['member_id' => $member->id, 'plan_id' => $plan->id],
            [
                'starts_at' => today()->subMonth(),
                'status' => 'active',
                'billing_day' => 10,
                'price' => $plan->price,
                'notes' => 'Matrícula de demonstração.',
            ],
        );

        app(BillingService::class)->generateCharges($membership);
    }
}
