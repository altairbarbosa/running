<?php

namespace App\Services;

use App\Models\Charge;
use App\Models\Membership;
use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillingService
{
    public function generateCharges(Membership $membership, ?CarbonInterface $until = null): int
    {
        if ($membership->status === 'cancelled') {
            return 0;
        }

        $membership->loadMissing('plan');
        $horizon = Carbon::instance($until ?? now()->addMonths(3))->endOfDay();
        if ($membership->ends_at && $membership->ends_at->lt($horizon)) {
            $horizon = $membership->ends_at->copy()->endOfDay();
        }

        $created = 0;

        if (bccomp($membership->plan->enrollment_fee, '0', 2) === 1) {
            $charge = $membership->charges()->firstOrCreate(
                ['type' => 'enrollment', 'due_date' => $membership->starts_at],
                [
                    'description' => 'Taxa de matrícula',
                    'amount' => $membership->plan->enrollment_fee,
                    'status' => $membership->starts_at->lt(today()) ? 'overdue' : 'pending',
                ],
            );
            $created += $charge->wasRecentlyCreated ? 1 : 0;
        }

        $dueDate = $membership->starts_at->copy();
        while ($dueDate->lte($horizon)) {
            $charge = $membership->charges()->firstOrCreate(
                ['type' => 'monthly', 'due_date' => $dueDate],
                [
                    'description' => 'Mensalidade '.$dueDate->translatedFormat('m/Y'),
                    'amount' => $membership->price,
                    'status' => $dueDate->lt(today()) ? 'overdue' : 'pending',
                ],
            );
            $created += $charge->wasRecentlyCreated ? 1 : 0;

            $month = $dueDate->copy()->startOfMonth()->addMonths($membership->plan->billing_interval_months);
            $dueDate = $month->day(min($membership->billing_day, $month->daysInMonth));
        }

        return $created;
    }

    public function registerPayment(Charge $charge, array $data, int $receivedBy): Payment
    {
        return DB::transaction(function () use ($charge, $data, $receivedBy) {
            $charge = Charge::query()->lockForUpdate()->findOrFail($charge->id);

            if ($charge->status === 'cancelled') {
                throw ValidationException::withMessages(['amount' => 'Não é possível pagar uma cobrança cancelada.']);
            }

            if (bccomp((string) $data['amount'], $charge->outstanding, 2) === 1) {
                throw ValidationException::withMessages(['amount' => 'O pagamento não pode ultrapassar o saldo de R$ '.number_format((float) $charge->outstanding, 2, ',', '.')]);
            }

            $payment = $charge->payments()->create([
                ...$data,
                'received_by' => $receivedBy,
            ]);

            $paidAmount = bcadd($charge->paid_amount, (string) $data['amount'], 2);
            $status = bccomp($paidAmount, $charge->total, 2) >= 0
                ? 'paid'
                : ($charge->due_date->lt(today()) ? 'overdue' : 'partial');

            $charge->update(['paid_amount' => $paidAmount, 'status' => $status]);

            return $payment;
        });
    }

    public function refreshOverdueStatuses(): int
    {
        return Charge::query()
            ->whereIn('status', ['pending', 'partial'])
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'overdue']);
    }
}
