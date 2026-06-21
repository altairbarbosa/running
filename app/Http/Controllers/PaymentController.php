<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function store(Request $request, Charge $charge, BillingService $billing)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', Rule::in(['cash', 'pix', 'credit_card', 'debit_card', 'bank_transfer'])],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $billing->registerPayment($charge, $data, $request->user()->id);

        return back()->with('success', 'Pagamento registrado com sucesso.');
    }
}
