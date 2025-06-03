{{-- resources/views/booking/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- Verifica se $targetClientId foi passado para a view --}}
            @isset($targetClientId)
                {{ __('Novo Agendamento (Admin)') }}
            @else
                {{ __('Faça seu Agendamento') }}
            @endisset
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Passa targetClientId para o componente Livewire se estiver definido, senão passa null --}}
                    @livewire('booking-process', ['targetUserId' => $targetClientId ?? null])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>