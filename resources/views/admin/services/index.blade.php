<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Serviços') }}
        </h2>
    </x-slot>
    <div>
        @livewire('admin.services.service-list')
    </div>
</x-app-layout>