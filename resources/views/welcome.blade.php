<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'WX Barber Shop') }}</title>

        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#C0A062">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-900 bg-cover bg-center selection:bg-red-500 selection:text-white" style="background-image: url('{{ asset('images/barber-bg.jpg') }}')">
             <div class="absolute inset-0 bg-black opacity-60"></div>
             <div class="relative max-w-7xl mx-auto p-6 lg:p-8 text-center">
                {{-- O resto do seu código da tela de boas-vindas continua aqui... --}}
             </div>
        </div>

        {{-- Script para registrar o Service Worker --}}
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').then(registration => {
                        console.log('Service Worker da Welcome Page registrado!');
                    }).catch(error => {
                        console.log('Falha ao registrar Service Worker:', error);
                    });
                });
            }
        </script>
    </body>
</html>