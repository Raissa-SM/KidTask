<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Painel principal da criança — tarefas do dia.
     * Será expandido na Fase 5 com a lista de tarefas e pontos.
     */
    public function index(): View
    {
        return view('child.dashboard');
    }
}
