<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Services\BillingService;
use Illuminate\Http\Request;

class MemberBillingController extends Controller
{
    public function __invoke(Request $request, BillingService $billing)
    {
        if ($request->user()->isStaff() && $request->user()->hasPermission('billing.manage')) {
            return redirect()->route('finance.index');
        }

        abort_unless($request->user()->role === 'member', 403);

        $billing->refreshOverdueStatuses();

        $memberships = $request->user()
            ->memberships()
            ->with(['plan', 'charges.payments'])
            ->latest()
            ->get();

        $openAmount = Charge::whereHas('membership', fn ($query) => $query->where('member_id', $request->user()->id))
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->get()
            ->sum(fn ($charge) => (float) $charge->outstanding);

        return view('portal.billing', [
            'memberships' => $memberships,
            'openAmount' => $openAmount,
        ]);
    }
}
