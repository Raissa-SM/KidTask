<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSameFamily
{
    /**
     * Garante que o usuário pertence a uma família antes de acessar
     * áreas que dependem de uma família cadastrada.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || auth()->user()->family_id === null) {
            abort(403, 'Você precisa estar em uma família para acessar esta área.');
        }

        return $next($request);
    }
}
