<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Envolvemos o campo com Alpine.js para a máscara --}}
        <div x-data="{
            applyPhoneMask(value) {
                if (!value) return '';
                const digitsOnly = value.replace(/\D/g, '').slice(0, 11);
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
            {{-- Alteramos o label e os atributos do input --}}
            <x-input-label for="phone_number" :value="__('Celular')" />
            <x-text-input
                id="phone_number"
                class="block mt-1 w-full"
                type="text"
                name="phone_number"
                :value="old('phone_number')"
                required
                autofocus
                autocomplete="username"
                x-on:input="event.target.value = applyPhoneMask(event.target.value)"
                placeholder="(99) 99999-9999"
                maxlength="15"
            />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>


        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>