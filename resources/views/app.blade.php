<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'BideshGomon') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @routes {{-- Ziggy Routes --}}
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"]) {{-- Vite Entry Point --}}
        @inertiaHead {{-- Inertia Head --}}
    </head>
    <body class="font-sans antialiased">
        @inertia {{-- This is where Vue mounts your pages --}}
    </body>
</html>