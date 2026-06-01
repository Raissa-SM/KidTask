<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel — KidTask</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <span class="text-xl font-bold text-indigo-600">KidTask</span>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">Olá, {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-gray-600">Sair</button>
            </form>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Painel do Responsável</h2>
        <p class="text-gray-500 mb-8">
            Família: <strong>{{ auth()->user()->family->name ?? '—' }}</strong>
            &nbsp;|&nbsp;
            Código de convite: <code class="bg-gray-100 px-2 py-0.5 rounded text-indigo-600 font-mono">{{ auth()->user()->family->invite_code ?? '—' }}</code>
        </p>

        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6 text-indigo-700">
            <p class="font-semibold">✅ Fase 3 concluída com sucesso!</p>
            <p class="text-sm mt-1">As funcionalidades de tarefas, validação e recompensas serão adicionadas nas próximas fases.</p>
        </div>
    </main>

</body>
</html>
