<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FamilyService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(private FamilyService $familyService)
    {
    }

    /**
     * Exibe o formulário de cadastro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Processa o cadastro do novo usuário.
     *
     * Fluxos possíveis:
     *   - role=parent + parent_action=create  → cria nova família com family_name
     *   - role=parent + parent_action=join    → entra em família existente pelo invite_code
     *   - role=child                          → entra em família existente pelo invite_code
     */
    public function store(Request $request): RedirectResponse
    {
        // Validação base — campos comuns a todos os perfis
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role'     => ['required', Rule::in(['parent', 'child'])],
        ]);

        // Validações condicionais por perfil e intenção
        if ($request->role === 'parent') {
            $request->validate([
                'parent_action' => ['required', Rule::in(['create', 'join'])],
            ]);

            if ($request->parent_action === 'create') {
                $request->validate([
                    'family_name' => ['required', 'string', 'max:255'],
                ]);
            } else {
                $request->validate([
                    'invite_code' => ['required', 'string'],
                ]);
            }
        } else {
            // Filho sempre precisa de código de convite
            $request->validate([
                'invite_code' => ['required', 'string'],
            ]);
        }

        // Cria o usuário — família será associada logo abaixo
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        try {
            if ($request->role === 'parent' && $request->parent_action === 'create') {
                // Pai cria uma nova família
                $this->familyService->createForUser($user, $request->family_name);
            } else {
                // Pai entrando em família existente OU filho — ambos usam o código de convite
                $this->familyService->joinByCode($user, $request->invite_code);
            }
        } catch (\InvalidArgumentException $e) {
            // Código inválido — remove o usuário recém-criado e volta com o erro
            $user->delete();

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['invite_code' => $e->getMessage()]);
        }

        event(new Registered($user));

        Auth::login($user);

        return $user->isParent()
            ? redirect()->route('parent.dashboard')
            : redirect()->route('child.dashboard');
    }
}
