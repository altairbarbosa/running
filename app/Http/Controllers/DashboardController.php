<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Exercise;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use App\Models\Workout;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request, BillingService $billing)
    {
        $user = $request->user();
        if ($user->isStaff()) {
            $billing->refreshOverdueStatuses();
        }
        $workouts = Workout::query()
            ->with('member')
            ->when(! $user->isStaff(), fn ($query) => $query->where('member_id', $user->id))
            ->latest('starts_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'memberCount' => $user->isStaff() ? User::where('role', 'member')->count() : null,
            'exerciseCount' => $user->isStaff() ? Exercise::where('active', true)->count() : null,
            'workoutCount' => $user->isStaff() ? Workout::where('status', 'active')->count() : $user->workouts()->where('status', 'active')->count(),
            'membershipCount' => $user->isStaff() ? Membership::where('status', 'active')->count() : null,
            'overdueAmount' => $user->isStaff() ? Charge::where('status', 'overdue')->sum(DB::raw('amount + late_fee - discount - paid_amount')) : null,
            'receivedThisMonth' => $user->isStaff() ? Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount') : null,
            'workouts' => $workouts,
        ]);
    }
}
