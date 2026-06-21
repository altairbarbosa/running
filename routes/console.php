<?php

use App\Models\Membership;
use App\Services\BillingService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('billing:generate', function (BillingService $billing) {
    $billing->refreshOverdueStatuses();
    $created = 0;
    Membership::query()->where('status', 'active')->each(function (Membership $membership) use ($billing, &$created) {
        $created += $billing->generateCharges($membership);
    });
    $this->info("{$created} cobrança(s) gerada(s).");
})->purpose('Gera próximas cobranças e atualiza inadimplências');

Schedule::command('billing:generate')->dailyAt('01:00');
