<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsParent
{
    /**
     * Garante que apenas pais/mães acessem as rotas protegidas.
     * Redireciona filhos para o próprio dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isParent()) {
            abort(403, 'Acesso restrito a responsáveis.');
        }

        return $next($request);
    }
}
