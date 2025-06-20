{{-- CORREÇÃO: Fundo escuro aplicado, preservando a lógica de perfis --}}
<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @auth
                        {{-- Se o usuário ESTÁ LOGADO --}}
                        <a href="{{ Auth::user()->is_admin ? route('admin.dashboard') : route('dashboard') }}">
                            <x-application-logo class="block h-24 w-auto fill-current text-gray-200" />
                        </a>
                    @else
                        {{-- Se o usuário NÃO ESTÁ LOGADO (visitante) --}}
                        <a href="{{ url('/') }}">
                            <x-application-logo class="block h-24 w-auto fill-current text-gray-200" />
                        </a>
                    @endauth
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if (Auth::user()->is_admin)
                            {{-- LINKS PARA ADMIN COM ÍCONES E NOVO ESTILO --}}
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.appointments.index')" :active="request()->routeIs('admin.appointments.*')">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0h18M-4.5 12h22.5" /></svg>
                                {{ __('Agendamentos') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.clients.index')" :active="request()->routeIs('admin.clients.*')">
                                 <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-2.438c.155-.155.295-.312.433-.472a11.942 11.942 0 0 0-7.53-2.281 11.942 11.942 0 0 0-1.531.076m-5.83-3.566a11.942 11.942 0 0 1 1.531-.076m5.83 3.566a11.942 11.942 0 0 1-5.83 3.566m0 0a11.942 11.942 0 0 1-1.531-.076m0 0a8.51 8.51 0 0 1-7.53-2.281 8.51 8.51 0 0 1-.433-.472c.138.16.277.317.433.472a8.51 8.51 0 0 0 4.121 2.438A8.51 8.51 0 0 0 9 19.5a8.51 8.51 0 0 0 1.531-.076M9 11.25a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm9 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                {{ __('Clientes') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m7.848 8.25 1.536 1.536m-1.536-1.536a3 3 0 0 0-4.243 0L2.25 9.75l4.243 4.242a3 3 0 0 0 4.242 0l1.06-1.06m-5.302-3.182a3.001 3.001 0 0 1 4.243 0L15 8.25l-4.243 4.242a3.001 3.001 0 0 1-4.242 0l-1.06-1.06m5.302-3.182 4.242-4.242a3 3 0 0 1 4.243 0l1.536 1.536a3 3 0 0 1 0 4.242l-4.242 4.242m-5.302-3.182-4.243 4.242" /></svg>
                                {{ __('Serviços') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.blocked-periods.index')" :active="request()->routeIs('admin.blocked-periods.*')">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                {{ __('Folgas/Bloqueios') }}
                            </x-nav-link>

                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <x-dropdown align="left" width="48">
                                    <x-slot name="trigger">
                                        @php
                                            $isFinancialActive = request()->routeIs('admin.financials.*') || request()->routeIs('admin.expense-categories.*') || request()->routeIs('admin.expenses.*');
                                            $baseClasses = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';
                                            // CORREÇÃO: Classes para o novo tema
                                            $activeClasses = ' border-brand-gold text-white focus:border-brand-gold';
                                            $inactiveClasses = ' border-transparent text-gray-400 hover:text-white hover:border-gray-500 focus:text-white focus:border-gray-500';
                                        @endphp
                                        <button class="{{ $baseClasses }} {{ $isFinancialActive ? $activeClasses : $inactiveClasses }}">
                                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                            <div>Financeiro</div>
                                            <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('admin.financials.index')">{{ __('Relatório de Faturamento') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.expense-categories.index')">{{ __('Categorias de Despesas') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.expenses.index')">{{ __('Lançar Despesas') }}</x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>

                        @else
                            {{-- LINKS PARA CLIENTES COM ÍCONES E NOVO ESTILO --}}
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" /></svg>
                                {{ __('Início') }}
                            </x-nav-link>
                            <x-nav-link :href="route('booking.index')" :active="request()->routeIs('booking.index')">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0h18M-4.5 12h22.5" /></svg>
                                {{ __('Agendar Horário') }}
                            </x-nav-link>
                            <x-nav-link :href="route('client.appointments.index')" :active="request()->routeIs('client.appointments.index')">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                                {{ __('Meus Agendamentos') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                             {{-- CORREÇÃO: Estilo do botão para tema escuro --}}
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-900 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    {{-- CORREÇÃO: Estilo dos links para tema escuro --}}
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white rounded-md">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ms-4 text-sm font-medium text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white rounded-md">Register</a>
                    @endif
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if (Auth::user()->is_admin)
                    {{-- LINKS RESPONSIVOS PARA ADMIN COM ÍCONES --}}
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.appointments.index')" :active="request()->routeIs('admin.appointments.*')">{{ __('Agendamentos') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.clients.index')" :active="request()->routeIs('admin.clients.*')">{{ __('Clientes') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">{{ __('Serviços') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.blocked-periods.index')" :active="request()->routeIs('admin.blocked-periods.*')">{{ __('Folgas/Bloqueios') }}</x-responsive-nav-link>
                    
                    <div class="pt-2 pb-1 border-t border-gray-600">
                        <div class="px-4"><div class="font-medium text-base text-gray-200">Financeiro</div></div>
                        <x-responsive-nav-link :href="route('admin.financials.index')" :active="request()->routeIs('admin.financials.*')">{{ __('Relatório de Faturamento') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.expense-categories.index')" :active="request()->routeIs('admin.expense-categories.*')">{{ __('Categorias de Despesas') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.expenses.index')" :active="request()->routeIs('admin.expenses.*')">{{ __('Lançar Despesas') }}</x-responsive-nav-link>
                    </div>
                @else
                    {{-- LINKS RESPONSIVOS PARA CLIENTES COM ÍCONES --}}
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Início') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('booking.index')" :active="request()->routeIs('booking.index')">{{ __('Agendar Horário') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('client.appointments.index')" :active="request()->routeIs('client.appointments.index')">{{ __('Meus Agendamentos') }}</x-responsive-nav-link>
                @endif
            @endauth
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="py-1 border-t border-gray-600">
                <x-responsive-nav-link :href="route('login')">Log in</x-responsive-nav-link>
                @if (Route::has('register'))
                    <x-responsive-nav-link :href="route('register')">Register</x-responsive-nav-link>
                @endif
            </div>
        @endauth
    </div>
</nav>