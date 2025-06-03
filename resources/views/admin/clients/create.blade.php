{{-- resources/views/admin/clients/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Criar Novo Cliente') }}
            </h2>
            <a href="{{ route('admin.clients.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">
                &larr; {{ __('Voltar para a Lista de Clientes') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.clients.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nome Completo')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="email" :value="__('E-mail')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="phone_number" :value="__('Número de Telefone')" />
                            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>
                        
                        {{--
                        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                            Uma senha aleatória será gerada para o cliente. Ele poderá usar a opção "Esqueci minha senha" para definir uma nova senha ou usar o Login Social se o e-mail corresponder.
                        </p>
                        --}}

                        <div class="flex items-center justify-end mt-6">
                             <x-secondary-button type="button" onclick="window.location='{{ route('admin.clients.index') }}'">
                                {{ __('Cancelar') }}
                            </x-secondary-button>
                            <x-primary-button class="ms-3">
                                {{ __('Criar Cliente') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>