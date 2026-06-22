<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ForcedPasswordController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MuscleGroupController;
use App\Http\Controllers\MemberBillingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePasswordController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\WorkoutTemplateController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/esqueci-minha-senha', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/esqueci-minha-senha', [PasswordResetController::class, 'sendLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/redefinir-senha/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/redefinir-senha', [PasswordResetController::class, 'reset'])->middleware('throttle:6,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/primeiro-acesso', [ForcedPasswordController::class, 'edit'])->name('password.change.edit');
    Route::put('/primeiro-acesso', [ForcedPasswordController::class, 'update'])->middleware('throttle:6,1')->name('password.change.update');
    Route::get('/dashboard', DashboardController::class)->middleware('permission:dashboard.view')->name('dashboard');
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/senha', [ProfilePasswordController::class, 'update'])->name('profile.password');
    Route::get('/treinos', [WorkoutController::class, 'index'])->middleware('permission:workouts.view')->name('workouts.index');
    Route::get('/treinos/{workout}', [WorkoutController::class, 'show'])->middleware('permission:workouts.view')->whereNumber('workout')->name('workouts.show');
    Route::get('/minhas-mensalidades', MemberBillingController::class)->middleware('permission:billing.view-own')->name('portal.billing');
    Route::get('/loja', [ShopController::class, 'index'])->middleware('permission:shop.view')->name('shop.index');
    Route::post('/loja/produtos/{product}/pedir', [ShopController::class, 'order'])->middleware('permission:shop.order')->name('shop.order');

    Route::resource('alunos', MemberController::class)->parameters(['alunos' => 'member'])->names('members')->only('index')->middleware('permission:members.view');
    Route::resource('alunos', MemberController::class)->parameters(['alunos' => 'member'])->names('members')->except(['index', 'show'])->middleware('permission:members.manage');
    Route::resource('exercicios', ExerciseController::class)->parameters(['exercicios' => 'exercise'])->names('exercises')->except('show')->middleware('permission:exercises.manage');
    Route::post('/grupos-musculares', [MuscleGroupController::class, 'store'])->middleware('permission:exercises.manage')->name('muscle-groups.store');
    Route::patch('/grupos-musculares/ordenar', [MuscleGroupController::class, 'reorder'])->middleware('permission:exercises.manage')->name('muscle-groups.reorder');
    Route::put('/grupos-musculares/{muscleGroup}', [MuscleGroupController::class, 'update'])->middleware('permission:exercises.manage')->name('muscle-groups.update');
    Route::post('/loja/produtos', [ShopController::class, 'storeProduct'])->middleware('permission:shop.manage')->name('shop.products.store');
    Route::put('/loja/produtos/{product}', [ShopController::class, 'updateProduct'])->middleware('permission:shop.manage')->name('shop.products.update');
    Route::resource('planos', PlanController::class)->parameters(['planos' => 'plan'])->names('plans')->except('show')->middleware('permission:plans.manage');
    Route::resource('matriculas', MembershipController::class)->parameters(['matriculas' => 'membership'])->names('memberships')->only(['index', 'create', 'store', 'show'])->middleware('permission:memberships.manage');
    Route::patch('/matriculas/{membership}/cancelar', [MembershipController::class, 'cancel'])->middleware('permission:memberships.manage')->name('memberships.cancel');
    Route::get('/financeiro', FinanceController::class)->middleware('permission:billing.manage')->name('finance.index');
    Route::post('/cobrancas/{charge}/pagamentos', [PaymentController::class, 'store'])->middleware('permission:billing.manage')->name('payments.store');
    Route::get('/treinos/novo/elaborar', [WorkoutController::class, 'create'])->middleware('permission:workouts.manage')->name('workouts.create');
    Route::post('/treinos', [WorkoutController::class, 'store'])->middleware('permission:workouts.manage')->name('workouts.store');
    Route::delete('/treinos/{workout}', [WorkoutController::class, 'destroy'])->middleware('permission:workouts.manage')->whereNumber('workout')->name('workouts.destroy');
    Route::post('/treinos/{workout}/modelo', [WorkoutTemplateController::class, 'storeFromWorkout'])->middleware('permission:workouts.manage')->name('workout-templates.store-from-workout');
    Route::delete('/modelos-treino/{workoutTemplate}', [WorkoutTemplateController::class, 'destroy'])->middleware('permission:workouts.manage')->name('workout-templates.destroy');

    Route::resource('usuarios', UserManagementController::class)->parameters(['usuarios' => 'user'])->names('users')->except('show')->middleware('permission:users.manage');
    Route::resource('permissoes', PermissionGroupController::class)->parameters(['permissoes' => 'permissionGroup'])->names('permissions')->except(['show', 'create', 'edit'])->middleware('permission:permissions.manage');
});
