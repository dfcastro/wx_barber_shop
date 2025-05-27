{{-- resources/views/admin/services/index.blade.php --}}

<x-app-layout> {{-- Ou o layout principal que o Breeze te forneceu --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gerenciar Serviços') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Exibir mensagem de sucesso --}}
            @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        {{-- Link para adicionar novo serviço (vamos criar a rota/página depois) --}}
                        <a href="{{ route('admin.services.create') }}" class="bg-gray-500 hover:bg-gray-700 text-black font-bold py-2 px-4 rounded">
                            Adicionar Novo Serviço
                        </a>
                    </div>

                    {{-- Aqui vamos colocar nosso componente Livewire para listar os serviços --}}
                    @livewire('admin.services.service-list', ['services' => $services])

                </div>
            </div>
        </div>
    </div>
</x-app-layout>