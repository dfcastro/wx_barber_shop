<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Períodos Bloqueados (dias de folga)') }}
        </h2>
    </x-slot>
    <div>
        @livewire('admin.blocked-periods.blocked-period-list')
    </div>
</x-app-layout>