<?php

use App\Http\Middleware\EnsureIsParent;
use App\Http\Middleware\EnsureIsSameFamily;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registra os aliases dos middlewares customizados
        // Uso nas rotas: 'parent' e 'family'
        $middleware->alias([
            'parent' => EnsureIsParent::class,
            'family' => EnsureIsSameFamily::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
