{{-- resources/views/admin/blocked-periods/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Período de Bloqueio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Passando o período para o formulário Livewire para edição --}}
                    @livewire('admin.blocked-periods.blocked-period-form', ['blockedPeriod' => $blockedPeriod])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>