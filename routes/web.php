<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Child\DashboardController as ChildDashboardController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\Parent\TaskController;
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
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

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
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])
            ->name('dashboard');

        // CRUD de tarefas — Fase 4
        Route::resource('tasks', TaskController::class)
            ->except(['show']); // show não é necessário: edição já exibe os detalhes

        // Fase 5: ValidationController será adicionado aqui
        // Fase 6: RewardController será adicionado aqui
    });

// ── Área da criança ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'family'])
    ->prefix('child')
    ->name('child.')
    ->group(function () {
        Route::get('/dashboard', [ChildDashboardController::class, 'index'])
            ->name('dashboard');

        // Fase 5: TaskCompletionController e PointController serão adicionados aqui
    });
