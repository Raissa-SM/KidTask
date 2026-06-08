<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Child\DashboardController as ChildDashboardController;
use App\Http\Controllers\Child\PointController;
use App\Http\Controllers\Child\RewardController as ChildRewardController;
use App\Http\Controllers\Child\TaskCompletionController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\Parent\RewardController as ParentRewardController;
use App\Http\Controllers\Parent\TaskController;
use App\Http\Controllers\Parent\ValidationController;
use Illuminate\Support\Facades\Route;

// ── Página inicial ────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isParent()
            ? redirect()->route('parent.dashboard')
            : redirect()->route('child.dashboard');
    }
    return redirect()->route('login');
});

// ── Rotas de autenticação (visitantes) ────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ── Área do pai/mãe ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'parent', 'family'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        // CRUD de tarefas — Fase 4
        Route::resource('tasks', TaskController::class)->except(['show']);

        // Validações — Fase 5
        Route::get('/validations', [ValidationController::class, 'index'])->name('validations.index');
        Route::post('/validations/{completion}/approve', [ValidationController::class, 'approve'])->name('validations.approve');
        Route::post('/validations/{completion}/reject', [ValidationController::class, 'reject'])->name('validations.reject');

        // CRUD de recompensas — Fase 6
        Route::resource('rewards', ParentRewardController::class)->except(['show']);
    });

// ── Área da criança ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'family'])
    ->prefix('child')
    ->name('child.')
    ->group(function () {
        Route::get('/dashboard', [ChildDashboardController::class, 'index'])->name('dashboard');

        // Conclusão de tarefas — Fase 5
        Route::post('/tasks/{task}/complete', [TaskCompletionController::class, 'store'])->name('tasks.complete');

        // Pontos — Fase 5
        Route::get('/points', [PointController::class, 'index'])->name('points');

        // Recompensas — Fase 6
        Route::get('/rewards', [ChildRewardController::class, 'index'])->name('rewards');
        Route::post('/rewards/{reward}/redeem', [ChildRewardController::class, 'redeem'])->name('rewards.redeem');
    });
