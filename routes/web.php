<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePasswordController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/senha', [ProfilePasswordController::class, 'update'])->name('profile.password');
    Route::get('/treinos', [WorkoutController::class, 'index'])->name('workouts.index');
    Route::get('/treinos/{workout}', [WorkoutController::class, 'show'])->whereNumber('workout')->name('workouts.show');

    Route::middleware('staff')->group(function () {
        Route::resource('alunos', MemberController::class)->parameters(['alunos' => 'member'])->names('members')->except('show');
        Route::resource('exercicios', ExerciseController::class)->parameters(['exercicios' => 'exercise'])->names('exercises')->except('show');
        Route::resource('planos', PlanController::class)->parameters(['planos' => 'plan'])->names('plans')->except('show');
        Route::resource('matriculas', MembershipController::class)->parameters(['matriculas' => 'membership'])->names('memberships')->only(['index', 'create', 'store', 'show']);
        Route::patch('/matriculas/{membership}/cancelar', [MembershipController::class, 'cancel'])->name('memberships.cancel');
        Route::get('/financeiro', FinanceController::class)->name('finance.index');
        Route::post('/cobrancas/{charge}/pagamentos', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/treinos/novo/elaborar', [WorkoutController::class, 'create'])->name('workouts.create');
        Route::post('/treinos', [WorkoutController::class, 'store'])->name('workouts.store');
        Route::delete('/treinos/{workout}', [WorkoutController::class, 'destroy'])->whereNumber('workout')->name('workouts.destroy');
    });

    Route::middleware('admin')->group(function () {
        Route::resource('usuarios', UserManagementController::class)->parameters(['usuarios' => 'user'])->names('users')->except('show');
    });
});
