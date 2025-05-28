{{-- resources/views/admin/blocked-periods/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gerenciar Dias de Folga / Períodos Bloqueados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4 flex justify-end">
                        <a href="{{ route('admin.blocked-periods.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Adicionar Novo Bloqueio
                        </a>
                    </div>

                    {{-- Componente Livewire para listar e gerenciar períodos bloqueados --}}
                    @livewire('admin.blocked-periods.blocked-period-list')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>