<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Service') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('admin.services.store') }}" method="POST">
                    @csrf

                    {{-- Campo Nome --}}
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Campo Descrição --}}
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea name="description" id="description" rows="3"
                                  class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- Campo Preço com Máscara de Moeda --}}
                    <div class="mb-4" x-data="{
                        applyCurrencyMask(value) {
                            if (!value) return '';
                            let digitsOnly = value.toString().replace(/\D/g, '');
                            if (digitsOnly.length === 0) return '';
                            let number = parseFloat(digitsOnly) / 100;
                            return number.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                        }
                    }">
                        <x-input-label for="price" :value="__('Price')" />
                        <x-text-input
                            id="price"
                            class="block mt-1 w-full"
                            type="text"
                            name="price"
                            x-on:input="event.target.value = applyCurrencyMask(event.target.value)"
                            :value="old('price')"
                            placeholder="R$ 0,00"
                            required
                        />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>

                    {{-- Campo Duração com Validação de Inteiro --}}
                    <div class="mb-4" x-data>
                        <x-input-label for="duration_minutes" :value="__('Duration (minutes)')" />
                        <x-text-input
                            id="duration_minutes"
                            class="block mt-1 w-full"
                            type="text"
                            inputmode="numeric" {{-- Melhora a experiência em celulares --}}
                            name="duration_minutes"
                            x-on:input="event.target.value = event.target.value.replace(/\D/g, '')"
                            :value="old('duration_minutes')"
                            required
                        />
                        <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                    </div>

                    {{-- Botões de Ação --}}
                    <div class="flex items-center justify-end mt-6">
                        <x-secondary-button type="button" onclick="window.location='{{ route('admin.services.index') }}'">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-primary-button class="ml-4">
                            {{ __('Save') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>