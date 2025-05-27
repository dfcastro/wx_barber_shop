{{-- resources/views/booking/index.blade.php --}}

<x-app-layout> {{-- Usando o layout principal do Breeze --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Faça seu Agendamento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Componente Livewire principal para o processo de agendamento --}}
                    @livewire('booking-process')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>