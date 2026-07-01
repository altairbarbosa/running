<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        return view('plans.index', [
            'plans' => Plan::withCount(['memberships' => fn ($query) => $query->where('status', 'active')])->orderBy('name')->get(),
            'modalPlan' => $request->integer('plan') ? Plan::find($request->integer('plan')) : null,
        ]);
    }

    public function create()
    {
        return redirect()->route('plans.index', ['modal' => 'create']);
    }

    public function store(Request $request)
    {
        Plan::create($this->validated($request));

        return redirect()->route('plans.index')->with('success', 'Plano cadastrado.');
    }

    public function edit(Plan $plan)
    {
        return redirect()->route('plans.index', ['modal' => 'edit', 'plan' => $plan->id]);
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->validated($request, $plan));

        return redirect()->route('plans.index')->with('success', 'Plano atualizado. As matrículas existentes mantiveram seus valores.');
    }

    public function destroy(Plan $plan)
    {
        $plan->update(['active' => false]);

        return back()->with('success', 'Plano desativado.');
    }

    private function validated(Request $request, ?Plan $plan = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('plans')->ignore($plan)],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'enrollment_fee' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'billing_interval_months' => ['required', 'integer', Rule::in([1, 3, 6, 12])],
            'description' => ['nullable', 'string', 'max:2000'],
            'active' => ['required', 'boolean'],
        ]);
    }
}
