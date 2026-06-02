<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro — KidTask</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md px-6 py-8">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-indigo-600">KidTask</h1>
        <p class="text-gray-500 mt-1">Crie sua conta e comece a organizar a família</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Nome --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nome completo
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror"
                    placeholder="Seu nome"
                >
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- E-mail --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    E-mail
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-400 @enderror"
                    placeholder="seu@email.com"
                >
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Senha --}}
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Senha
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-400 @enderror"
                    placeholder="Mínimo 8 caracteres"
                >
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmar senha --}}
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirmar senha
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Repita a senha"
                >
            </div>

            {{-- Perfil: pai ou filho --}}
            <div class="mb-5">
                <p class="block text-sm font-medium text-gray-700 mb-2">Você é:</p>
                <div class="grid grid-cols-2 gap-3">

                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            name="role"
                            value="parent"
                            class="peer sr-only"
                            {{ old('role', 'parent') === 'parent' ? 'checked' : '' }}
                            onchange="toggleRoleFields('parent')"
                        >
                        <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                            <span class="text-2xl mb-1">👨‍👩‍👧</span>
                            <span class="text-sm font-medium text-gray-700">Pai / Mãe</span>
                            <span class="text-xs text-gray-400">Gerencia a família</span>
                        </div>
                    </label>

                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            name="role"
                            value="child"
                            class="peer sr-only"
                            {{ old('role') === 'child' ? 'checked' : '' }}
                            onchange="toggleRoleFields('child')"
                        >
                        <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                            <span class="text-2xl mb-1">🧒</span>
                            <span class="text-sm font-medium text-gray-700">Filho / Filha</span>
                            <span class="text-xs text-gray-400">Entra na família</span>
                        </div>
                    </label>

                </div>
                @error('role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ── Bloco do pai ──────────────────────────────────────────────── --}}
            <div id="block-parent" class="{{ old('role') === 'child' ? 'hidden' : '' }}">

                {{-- Sub-opção: criar nova família ou entrar em uma existente --}}
                <div class="mb-4">
                    <p class="block text-sm font-medium text-gray-700 mb-2">O que deseja fazer?</p>
                    <div class="grid grid-cols-2 gap-3">

                        <label class="relative cursor-pointer">
                            <input
                                type="radio"
                                name="parent_action"
                                value="create"
                                class="peer sr-only"
                                {{ old('parent_action', 'create') !== 'join' ? 'checked' : '' }}
                                onchange="toggleParentAction('create')"
                            >
                            <div class="flex flex-col items-center p-3 rounded-xl border-2 border-gray-200
                                        peer-checked:border-indigo-400 peer-checked:bg-indigo-50 transition">
                                <span class="text-xl mb-1">🏠</span>
                                <span class="text-xs font-medium text-gray-700 text-center">Criar nova família</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input
                                type="radio"
                                name="parent_action"
                                value="join"
                                class="peer sr-only"
                                {{ old('parent_action') === 'join' ? 'checked' : '' }}
                                onchange="toggleParentAction('join')"
                            >
                            <div class="flex flex-col items-center p-3 rounded-xl border-2 border-gray-200
                                        peer-checked:border-indigo-400 peer-checked:bg-indigo-50 transition">
                                <span class="text-xl mb-1">🔗</span>
                                <span class="text-xs font-medium text-gray-700 text-center">Entrar em família existente</span>
                            </div>
                        </label>

                    </div>
                    @error('parent_action')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo: Nome da família (criar) --}}
                <div id="field-family-name" class="mb-4 {{ old('parent_action') === 'join' ? 'hidden' : '' }}">
                    <label for="family_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nome da família
                    </label>
                    <input
                        type="text"
                        id="family_name"
                        name="family_name"
                        value="{{ old('family_name') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('family_name') border-red-400 @enderror"
                        placeholder="Ex: Família Silva"
                    >
                    @error('family_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo: Código de convite (entrar como pai) --}}
                <div id="field-parent-invite" class="mb-4 {{ old('parent_action') !== 'join' ? 'hidden' : '' }}">
                    <label for="invite_code_parent" class="block text-sm font-medium text-gray-700 mb-1">
                        Código de convite da família
                    </label>
                    <input
                        type="text"
                        id="invite_code_parent"
                        name="invite_code"
                        value="{{ old('invite_code') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('invite_code') border-red-400 @enderror"
                        placeholder="Ex: ABC123"
                        maxlength="6"
                    >
                    <p class="text-gray-400 text-xs mt-1">Peça o código para o responsável que criou a família.</p>
                    @error('invite_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
            {{-- ── Fim do bloco do pai ───────────────────────────────────────── --}}

            {{-- ── Bloco do filho ───────────────────────────────────────────── --}}
            <div id="block-child" class="mb-4 {{ old('role') !== 'child' ? 'hidden' : '' }}">
                <label for="invite_code_child" class="block text-sm font-medium text-gray-700 mb-1">
                    Código de convite
                </label>
                <input
                    type="text"
                    id="invite_code_child"
                    name="invite_code"
                    value="{{ old('role') === 'child' ? old('invite_code') : '' }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('invite_code') border-red-400 @enderror"
                    placeholder="Ex: ABC123"
                    maxlength="6"
                >
                <p class="text-gray-400 text-xs mt-1">Peça o código para seu responsável.</p>
                @error('invite_code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            {{-- ── Fim do bloco do filho ────────────────────────────────────── --}}

            <button
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition mt-2"
            >
                Criar conta
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Já tem conta?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">
                Entrar
            </a>
        </p>

    </div>
</div>

<script>
    /**
     * Alterna os blocos de campos conforme o perfil selecionado (pai ou filho).
     * Sincroniza o campo invite_code entre os dois inputs para manter old() funcionando.
     */
    function toggleRoleFields(role) {
        document.getElementById('block-parent').classList.toggle('hidden', role !== 'parent');
        document.getElementById('block-child').classList.toggle('hidden', role !== 'child');

        // Sincroniza o valor do código de convite entre os dois campos
        const parentInvite = document.getElementById('invite_code_parent');
        const childInvite  = document.getElementById('invite_code_child');
        if (role === 'child') {
            childInvite.value = parentInvite.value;
        } else {
            parentInvite.value = childInvite.value;
        }
    }

    /**
     * Alterna os sub-campos do pai: nome da família (criar) ou código (entrar).
     */
    function toggleParentAction(action) {
        document.getElementById('field-family-name').classList.toggle('hidden', action !== 'create');
        document.getElementById('field-parent-invite').classList.toggle('hidden', action !== 'join');
    }

    // Aplica o estado correto ao carregar (importante para old() após erro de validação)
    document.addEventListener('DOMContentLoaded', function () {
        const roleChecked   = document.querySelector('input[name="role"]:checked');
        const actionChecked = document.querySelector('input[name="parent_action"]:checked');

        if (roleChecked)   toggleRoleFields(roleChecked.value);
        if (actionChecked) toggleParentAction(actionChecked.value);
    });

    /**
     * Antes do submit, desabilita o campo invite_code que está no bloco oculto.
     * Isso evita que o PHP receba dois valores para o mesmo campo e pegue o errado.
     */
    document.querySelector('form').addEventListener('submit', function () {
        const role         = document.querySelector('input[name="role"]:checked')?.value;
        const parentAction = document.querySelector('input[name="parent_action"]:checked')?.value;

        if (role === 'child') {
            // Desabilita o campo de código do bloco do pai (oculto)
            document.getElementById('invite_code_parent').disabled = true;
        } else if (role === 'parent' && parentAction === 'create') {
            // Desabilita o campo de código do pai (sub-bloco oculto)
            document.getElementById('invite_code_parent').disabled = true;
            document.getElementById('invite_code_child').disabled  = true;
        } else if (role === 'parent' && parentAction === 'join') {
            // Desabilita o campo de código do filho (bloco oculto)
            document.getElementById('invite_code_child').disabled = true;
        }
    });
</script>

</body>
</html>
