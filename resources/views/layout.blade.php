<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('laravel.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-900 text-slate-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-slate-800 p-4">
            <div class="container mx-auto">
                <h1 class="text-2xl font-bold"><a href="/" class="flex items-center gap-4"><img style="width: 35px;" src="{{ asset('images/laravel.svg') }}">{{ config('app.name', 'Laravel') }}</a></h1>
            </div>
        </header>
        <main class="flex-grow container mx-auto p-4 pt-6 pb-14">
            @yield('content')
        </main>
        <footer class="bg-slate-800 p-4 text-center">
            <div class="container mx-auto">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}</p>
            </div>
        </footer>
    </div>
</body>

</html>