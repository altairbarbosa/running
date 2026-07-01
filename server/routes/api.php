<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\RunningApiController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ['status' => 'ok']);

Route::post('/auth/login', [AuthApiController::class, 'login'])->middleware('throttle:6,1');
Route::post('/auth/forgot-password', [AuthApiController::class, 'forgotPassword'])->middleware('throttle:3,1');
Route::post('/auth/reset-password', [AuthApiController::class, 'resetPassword'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::get('/auth/me', [AuthApiController::class, 'me']);
    Route::put('/auth/change-temporary-password', [AuthApiController::class, 'changeTemporaryPassword'])->middleware('throttle:6,1');

    Route::get('/profile', [AuthApiController::class, 'profile']);
    Route::put('/profile', [AuthApiController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthApiController::class, 'updatePassword']);

    Route::get('/dashboard', [RunningApiController::class, 'dashboard'])->middleware('permission:dashboard.view');

    Route::get('/members', [RunningApiController::class, 'members'])->middleware('permission:members.view');
    Route::post('/members', [RunningApiController::class, 'storeMember'])->middleware('permission:members.manage');
    Route::put('/members/{member}', [RunningApiController::class, 'updateMember'])->middleware('permission:members.manage');
    Route::delete('/members/{member}', [RunningApiController::class, 'destroyMember'])->middleware('permission:members.manage');

    Route::get('/users', [RunningApiController::class, 'users'])->middleware('permission:users.manage');
    Route::post('/users', [RunningApiController::class, 'storeUser'])->middleware('permission:users.manage');
    Route::put('/users/{user}', [RunningApiController::class, 'updateUser'])->middleware('permission:users.manage');
    Route::delete('/users/{user}', [RunningApiController::class, 'destroyUser'])->middleware('permission:users.manage');

    Route::get('/permission-groups', [RunningApiController::class, 'permissionGroups'])->middleware('permission:permissions.manage');
    Route::post('/permission-groups', [RunningApiController::class, 'storePermissionGroup'])->middleware('permission:permissions.manage');
    Route::put('/permission-groups/{permissionGroup}', [RunningApiController::class, 'updatePermissionGroup'])->middleware('permission:permissions.manage');
    Route::delete('/permission-groups/{permissionGroup}', [RunningApiController::class, 'destroyPermissionGroup'])->middleware('permission:permissions.manage');

    Route::get('/exercises', [RunningApiController::class, 'exercises'])->middleware('permission:exercises.manage');
    Route::post('/exercises', [RunningApiController::class, 'storeExercise'])->middleware('permission:exercises.manage');
    Route::put('/exercises/{exercise}', [RunningApiController::class, 'updateExercise'])->middleware('permission:exercises.manage');
    Route::delete('/exercises/{exercise}', [RunningApiController::class, 'destroyExercise'])->middleware('permission:exercises.manage');
    Route::post('/muscle-groups', [RunningApiController::class, 'storeMuscleGroup'])->middleware('permission:exercises.manage');
    Route::put('/muscle-groups/{muscleGroup}', [RunningApiController::class, 'updateMuscleGroup'])->middleware('permission:exercises.manage');
    Route::patch('/muscle-groups/reorder', [RunningApiController::class, 'reorderMuscleGroups'])->middleware('permission:exercises.manage');

    Route::get('/workouts', [RunningApiController::class, 'workouts'])->middleware('permission:workouts.view');
    Route::get('/workouts/form-data', [RunningApiController::class, 'workoutForm'])->middleware('permission:workouts.manage');
    Route::post('/workouts', [RunningApiController::class, 'storeWorkout'])->middleware('permission:workouts.manage');
    Route::get('/workouts/{workout}', [RunningApiController::class, 'workout'])->middleware('permission:workouts.view')->whereNumber('workout');
    Route::delete('/workouts/{workout}', [RunningApiController::class, 'destroyWorkout'])->middleware('permission:workouts.manage')->whereNumber('workout');
    Route::post('/workouts/{workout}/template', [RunningApiController::class, 'storeWorkoutTemplate'])->middleware('permission:workouts.manage')->whereNumber('workout');
    Route::delete('/workout-templates/{workoutTemplate}', [RunningApiController::class, 'destroyWorkoutTemplate'])->middleware('permission:workouts.manage');

    Route::get('/shop', [RunningApiController::class, 'shop'])->middleware('permission:shop.view');
    Route::post('/shop/products', [RunningApiController::class, 'storeProduct'])->middleware('permission:shop.manage');
    Route::post('/shop/products/{product}', [RunningApiController::class, 'updateProduct'])->middleware('permission:shop.manage');
    Route::post('/shop/products/{product}/order', [RunningApiController::class, 'orderProduct'])->middleware('permission:shop.order');

    Route::get('/plans', [RunningApiController::class, 'plans'])->middleware('permission:plans.manage');
    Route::post('/plans', [RunningApiController::class, 'storePlan'])->middleware('permission:plans.manage');
    Route::put('/plans/{plan}', [RunningApiController::class, 'updatePlan'])->middleware('permission:plans.manage');
    Route::delete('/plans/{plan}', [RunningApiController::class, 'destroyPlan'])->middleware('permission:plans.manage');

    Route::get('/memberships', [RunningApiController::class, 'memberships'])->middleware('permission:memberships.manage');
    Route::post('/memberships', [RunningApiController::class, 'storeMembership'])->middleware('permission:memberships.manage');
    Route::get('/memberships/{membership}', [RunningApiController::class, 'membership'])->middleware('permission:memberships.manage');
    Route::patch('/memberships/{membership}/cancel', [RunningApiController::class, 'cancelMembership'])->middleware('permission:memberships.manage');

    Route::get('/finance', [RunningApiController::class, 'finance'])->middleware('permission:billing.manage');
    Route::post('/charges/{charge}/payments', [RunningApiController::class, 'storePayment'])->middleware('permission:billing.manage');
    Route::get('/my-billing', [RunningApiController::class, 'ownBilling'])->middleware('permission:billing.view-own');
});
