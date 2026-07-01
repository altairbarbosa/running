<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Payment;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __invoke(Request $request, BillingService $billing)
    {
        $billing->refreshOverdueStatuses();
        $charges = Charge::with(['membership.member', 'membership.plan'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('membership.member', fn ($member) => $member->where('name', 'like', '%'.$request->string('search')->trim().'%'));
            })
            ->orderByDesc('due_date')->paginate(20)->withQueryString();

        $openExpression = 'amount + late_fee - discount - paid_amount';

        return view('finance.index', [
            'charges' => $charges,
            'openAmount' => Charge::whereIn('status', ['pending', 'partial', 'overdue'])->sum(DB::raw($openExpression)),
            'overdueAmount' => Charge::where('status', 'overdue')->sum(DB::raw($openExpression)),
            'receivedThisMonth' => Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
        ]);
    }
}
