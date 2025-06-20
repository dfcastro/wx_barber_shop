<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'WX Barber Shop') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-900 bg-cover bg-center selection:bg-red-500 selection:text-white" style="background-image: url('{{ asset('images/barber-bg.jpg') }}')">
             {{-- Overlay escuro --}}
            <div class="absolute inset-0 bg-black opacity-60"></div>

            <div class="relative max-w-7xl mx-auto p-6 lg:p-8 text-center">
                <div class="flex justify-center">
                   <img src="{{ asset('images/logo-barbearia.png') }}" alt="Logo Barbearia" class="w-52 h-auto">
                </div>

                <div class="mt-8">
                    <h1 class="text-4xl font-bold text-white tracking-wider">
                        Estilo e Tradição em Cada Corte
                    </h1>
                    <p class="mt-4 text-lg text-gray-300">
                        Sua experiência de barbearia elevada a um novo nível. Agende seu horário com os melhores profissionais.
                    </p>
                </div>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto rounded-md px-8 py-3 text-base font-semibold text-white shadow-sm ring-2 ring-brand-gold hover:bg-brand-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-gold transition-colors duration-200">Acessar Painel</a>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto rounded-md px-8 py-3 text-base font-semibold text-white shadow-sm ring-2 ring-brand-gold hover:bg-brand-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-gold transition-colors duration-200">Entrar</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="w-full sm:w-auto rounded-md px-8 py-3 text-base font-semibold text-gray-900 bg-gray-200 hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors duration-200">Criar Conta</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </body>
</html>