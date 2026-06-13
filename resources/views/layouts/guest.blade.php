<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KidTask') — KidTask</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-4 sm:px-6 py-6 sm:py-8">

        <div class="text-center mb-6 sm:mb-8">
            <a href="/" class="text-3xl font-bold text-indigo-600">KidTask</a>
            <p class="text-gray-500 mt-1 text-sm">@yield('subtitle', 'Organize as tarefas da sua família')</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            @yield('content')
        </div>

    </div>

</body>
</html>
