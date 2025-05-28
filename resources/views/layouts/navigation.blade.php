{{-- resources/views/layouts/navigation.blade.php --}}
<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}"> {{-- Ou route('home') ou '/' --}}
                        {{-- Substitua pelo seu logo. Ex: <x-application-logo class="block h-20 w-auto fill-current text-gray-800 dark:text-gray-200" /> --}}
                        <img src="/images/wx_logo.png" alt="WX Barber Shop Logo" class="block h-20 w-auto"> {{-- Exemplo de logo --}}
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth {{-- Links para usuários autenticados --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }} {{-- Ou Painel Principal, etc. --}}
                    </x-nav-link>

                    {{-- Links do Administrador --}}
                    @if (Auth::user()->is_admin)
                    <x-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.index') || request()->routeIs('admin.services.create') || request()->routeIs('admin.services.edit')">
                        {{ __('Serviços') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.appointments.index')" :active="request()->routeIs('admin.appointments.index')">
                        {{ __('Agendamentos') }}
                    </x-nav-link>
                    @endif

                    {{-- Adicione aqui outros links para clientes logados, se houver --}}
                    {{-- Ex: "Meus Agendamentos" para clientes --}}
                    {{-- @if (!Auth::user()->is_admin)
                            <x-nav-link :href="route('client.appointments.index')" :active="request()->routeIs('client.appointments.index')">
                                {{ __('Meus Agendamentos') }}
                    </x-nav-link>
                    @endif --}}
                    <x-nav-link :href="route('admin.blocked-periods.index')" :active="request()->routeIs('admin.blocked-periods.*')">
                        {{ __('Bloqueios/Folgas') }}
                    </x-nav-link>

                    @else {{-- Links para convidados (não autenticados) --}}
                    <x-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-nav-link>

                    @if (Route::has('register'))
                    <x-nav-link :href="route('register')">
                        {{ __('Registrar') }}
                    </x-nav-link>
                    @endif
                    @endauth
                </div>
            </div>

            @auth
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @auth
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- Links Responsivos do Administrador --}}
            @if (Auth::user()->is_admin)
            <x-responsive-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.index') || request()->routeIs('admin.services.create') || request()->routeIs('admin.services.edit')">
                {{ __('Serviços') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.appointments.index')" :active="request()->routeIs('admin.appointments.index')">
                {{ __('Agendamentos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.blocked-periods.index')" :active="request()->routeIs('admin.blocked-periods.*')">
                {{ __('Bloqueios/Folgas') }}
            </x-responsive-nav-link>
            @endif

            {{-- Adicione aqui outros links responsivos para clientes logados, se houver --}}
            {{-- @if (!Auth::user()->is_admin)
                    <x-responsive-nav-link :href="route('client.appointments.index')" :active="request()->routeIs('client.appointments.index')">
                        {{ __('Meus Agendamentos') }}
            </x-responsive-nav-link>
            @endif --}}
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                            this.closest('form').submit();">
                        {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        {{-- Links Responsivos para Convidados --}}
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('login')">
                {{ __('Login') }}
            </x-responsive-nav-link>
            @if (Route::has('register'))
            <x-responsive-nav-link :href="route('register')">
                {{ __('Registrar') }}
            </x-responsive-nav-link>
            @endif
        </div>
        @endauth
    </div>
</nav>