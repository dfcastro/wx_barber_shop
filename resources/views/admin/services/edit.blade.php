{{-- resources/views/admin/services/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Serviço') }}: {{ $service->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Componente Livewire para o formulário de serviço, passando o serviço a ser editado --}}
                    @livewire('admin.services.service-form', ['service' => $service])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>