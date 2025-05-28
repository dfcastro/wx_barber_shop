{{-- resources/views/client/appointments/index.blade.php --}}

<x-app-layout> {{-- Usando o layout principal do Breeze --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Meus Agendamentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Componente Livewire para listar os agendamentos do cliente --}}
                    @livewire('client.my-appointments-list')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>