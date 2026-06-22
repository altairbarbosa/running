<?php
namespace App\Http\Controllers;
use App\Models\Charge;
use App\Services\BillingService;
use Illuminate\Http\Request;

class MemberBillingController extends Controller
{
    public function __invoke(Request $request, BillingService $billing)
    {
        abort_unless($request->user()->role === 'member', 403); $billing->refreshOverdueStatuses();
        $memberships = $request->user()->memberships()->with(['plan','charges.payments'])->latest()->get();
        return view('portal.billing', ['memberships'=>$memberships, 'openAmount'=>Charge::whereHas('membership', fn($q)=>$q->where('member_id',$request->user()->id))->whereIn('status',['pending','partial','overdue'])->get()->sum(fn($c)=>(float)$c->outstanding)]);
    }
}
