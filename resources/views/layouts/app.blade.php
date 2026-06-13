<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KidTask') — KidTask</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-gray-50 min-h-screen">

    @include('layouts.partials.nav')

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-4 sm:py-8">
        @include('layouts.partials.alerts')
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>
