<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-white">
    <div class="min-h-screen flex flex-col sm:flex-row">
        <!-- Left side (form area) -->
        <div class="w-full sm:w-1/2 flex items-center justify-center bg-white p-8">
            <div class="max-w-md w-full">
                {{ $slot }}
            </div>
        </div>

        <!-- Right side (illustration area) -->
        <div class="hidden sm:flex sm:w-1/2 items-center justify-center bg-red-700">
            <img src="{{ asset('images/gambarsatu.png') }}" alt="Login Illustration" class="w-3/4 h-auto">
        </div>
    </div>
</body>
</html>
