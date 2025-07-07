<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#C0A062">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    {{-- SCRIPTS DO LIVEWIRE AQUI, ANTES DE FECHAR O BODY --}}
    <script>
        // Registra o Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('Service Worker registrado com sucesso!');
                }).catch(error => {
                    console.log('Falha ao registrar o Service Worker:', error);
                });
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- Opcional: Para tradução para Português --}}
    <script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>
    @livewireScripts
    @stack('scripts')
    {{-- ... (resto do seu arquivo app.blade.php) ... --}}


</body>
</body>

</html>