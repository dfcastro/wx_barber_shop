<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('admin.clients.store') }}" method="POST">
                    @csrf

                    {{-- Campo Nome --}}
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Campo E-mail --}}
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Campo Telefone com Máscara --}}
                    <div class="mb-4" x-data="{
                        applyPhoneMask(value) {
                            // Remove tudo que não for dígito
                            const digitsOnly = value.replace(/\D/g, '').slice(0, 11);

                            // Aplica a máscara (99) 99999-9999
                            let maskedValue = '';
                            if (digitsOnly.length > 0) {
                                maskedValue = '(' + digitsOnly.substring(0, 2);
                            }
                            if (digitsOnly.length > 2) {
                                maskedValue += ') ' + digitsOnly.substring(2, 7);
                            }
                            if (digitsOnly.length > 7) {
                                maskedValue += '-' + digitsOnly.substring(7, 11);
                            }
                            return maskedValue;
                        }
                    }">
                        <x-input-label for="phone_number" :value="__('Phone Number')" />
                        <x-text-input
                            id="phone_number"
                            class="block mt-1 w-full"
                            type="text"
                            name="phone_number"
                            :value="old('phone_number')"
                            x-on:input="event.target.value = applyPhoneMask(event.target.value)"
                            placeholder="(99) 99999-9999"
                            maxlength="15"
                        />
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                    </div>


                    {{-- Botões de Ação --}}
                    <div class="flex items-center justify-end mt-6">
                        <x-secondary-button type="button" onclick="window.location='{{ route('admin.clients.index') }}'">
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