<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="theme-color" content="#4F46E5">
        <meta name="format-detection" content="telephone=no">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- PWA Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-192x192.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/icons/icon-192x192.png">
        <meta name="msapplication-TileColor" content="#4F46E5">
        <meta name="msapplication-TileImage" content="/images/icons/icon-144x144.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @if(app()->environment('testing'))
            @vite(['resources/js/app.js'])
        @else
            @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @endif
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
