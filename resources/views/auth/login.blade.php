<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — KidTask</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

<div class="w-full max-w-sm px-6 py-8">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-indigo-600">KidTask</h1>
        <p class="text-gray-500 mt-1">Entre na sua conta</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        {{-- Erro de sessão (ex: credenciais inválidas) --}}
        @if (session('status'))
            <div class="mb-4 text-sm text-green-600 bg-green-50 rounded-lg px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

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
                    autofocus
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-400 @enderror"
                    placeholder="seu@email.com"
                >
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Senha --}}
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Senha
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-400 @enderror"
                    placeholder="Sua senha"
                >
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Lembrar de mim --}}
            <div class="flex items-center mb-6">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="rounded border-gray-300 text-indigo-600 mr-2"
                >
                <label for="remember" class="text-sm text-gray-600">Lembrar de mim</label>
            </div>

            <button
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition"
            >
                Entrar
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Não tem conta?
            <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">
                Cadastrar-se
            </a>
        </p>

    </div>
</div>

</body>
</html>
