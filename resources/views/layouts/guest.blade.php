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
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{-- ... O resto do seu layout de guest continua aqui ... --}}
    </body>
</html>
        {{-- Contêiner principal com a imagem de fundo --}}
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-900 bg-cover bg-center" style="background-image: url('{{ asset('images/barber-bg.jpg') }}')">
            {{-- Overlay escuro para melhorar a legibilidade --}}
            <div class="absolute inset-0 bg-black opacity-60"></div>

            {{-- Conteúdo fica acima do overlay --}}
            <div class="relative z-10 w-full sm:max-w-md">
                {{-- Logo exibido acima do card --}}
                <div class="flex justify-center mb-4">
                    <a href="/">
                        <img src="{{ asset('images/logo-barbearia.png') }}" alt="Logo Barbearia" class="w-52 h-auto">
                    </a>
                </div>

                {{-- O card onde o formulário de login/cadastro aparecerá --}}
                <div class="w-full px-6 py-4 bg-gray-800 bg-opacity-80 shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>