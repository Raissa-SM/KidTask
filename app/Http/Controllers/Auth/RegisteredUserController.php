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
     * - Se role = parent: cria uma nova família.
     * - Se role = child:  entra na família pelo código de convite.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validação base (campos comuns a ambos os perfis)
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role'     => ['required', Rule::in(['parent', 'child'])],
        ]);

        // Validações adicionais por perfil
        if ($request->role === 'parent') {
            $request->validate([
                'family_name' => ['required', 'string', 'max:255'],
            ]);
        } else {
            $request->validate([
                'invite_code' => ['required', 'string'],
            ]);
        }

        // Cria o usuário sem família ainda (será associado logo abaixo)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        try {
            if ($request->role === 'parent') {
                // Pai cria uma nova família
                $this->familyService->createForUser($user, $request->family_name);
            } else {
                // Filho entra na família pelo código de convite
                $this->familyService->joinByCode($user, $request->invite_code);
            }
        } catch (\InvalidArgumentException $e) {
            // Código de convite inválido — desfaz o usuário criado e volta com erro
            $user->delete();

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['invite_code' => $e->getMessage()]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redireciona para o dashboard correto conforme o perfil
        return $user->isParent()
            ? redirect()->route('parent.dashboard')
            : redirect()->route('child.dashboard');
    }
}
