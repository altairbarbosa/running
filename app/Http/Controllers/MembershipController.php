<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Plan;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $memberships = Membership::with(['member', 'plan'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('member', fn ($member) => $member->where('name', 'like', '%'.$request->string('search')->trim().'%'));
            })
            ->latest()->paginate(15)->withQueryString();

        return view('memberships.index', compact('memberships'));
    }

    public function create()
    {
        return view('memberships.create', [
            'members' => User::where('role', 'member')->where('active', true)->orderBy('name')->get(),
            'plans' => Plan::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, BillingService $billing)
    {
        $data = $request->validate([
            'member_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'member')->where('active', true))],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('active', true)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'billing_day' => ['required', 'integer', 'between:1,28'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (Membership::where('member_id', $data['member_id'])->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['member_id' => 'Este aluno já possui uma matrícula ativa.']);
        }

        $membership = DB::transaction(function () use ($data, $billing) {
            $plan = Plan::findOrFail($data['plan_id']);
            $membership = Membership::create([...$data, 'price' => $plan->price, 'status' => 'active']);
            $billing->generateCharges($membership);

            return $membership;
        });

        return redirect()->route('memberships.show', $membership)->with('success', 'Matrícula criada e cobranças geradas.');
    }

    public function show(Membership $membership, BillingService $billing)
    {
        $billing->refreshOverdueStatuses();
        $membership->load(['member', 'plan', 'charges.payments.receiver']);

        return view('memberships.show', compact('membership'));
    }

    public function cancel(Membership $membership)
    {
        DB::transaction(function () use ($membership) {
            $membership->update(['status' => 'cancelled', 'ends_at' => $membership->ends_at ?? today()]);
            $membership->charges()->whereIn('status', ['pending', 'partial'])->whereDate('due_date', '>=', today())->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Matrícula cancelada; cobranças futuras foram canceladas.');
    }
}
