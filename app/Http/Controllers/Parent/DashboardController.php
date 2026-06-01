<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Painel principal do pai/mãe.
     * Será expandido na Fase 5 com resumos e validações pendentes.
     */
    public function index(): View
    {
        return view('parent.dashboard');
    }
}
