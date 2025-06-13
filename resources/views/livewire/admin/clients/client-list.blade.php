<div>
    {{-- Cabeçalho com Busca e Botão de Criar --}}
    <div class="flex justify-between items-center mb-6">
        {{-- Campo de Busca (Alinhado à esquerda) --}}
        <div class="w-full md:w-1/2 lg:w-1/3">
            <x-text-input wire:model.live.debounce.300ms="search" type="text" class="block w-full"
                placeholder="Buscar por nome, e-mail..." />
        </div>

        {{-- Botão de Criar (Alinhado à direita) --}}
        <x-button.create href="{{ route('admin.clients.create') }}">
            Novo Cliente
        </x-button.create>
    </div>


    {{-- Mensagens de Feedback --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded"
            role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 dark:text-blue-300 dark:bg-blue-900 dark:border-blue-700 rounded"
            role="alert">
            {{ session('info') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 dark:text-red-300 dark:bg-red-900 dark:border-red-700 rounded"
            role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Seção de Filtros --}}
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow">
        <h3 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-3">Filtrar Clientes:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="filterAccountStatus"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status da Conta:</label>
                <select wire:model.live="filterAccountStatus" id="filterAccountStatus"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Todos</option>
                    <option value="active">Ativos</option>
                    <option value="inactive">Inativos</option>
                </select>
            </div>
            <div>
                <label for="filterEmailVerified"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail Verificado:</label>
                <select wire:model.live="filterEmailVerified" id="filterEmailVerified"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Todos</option>
                    <option value="verified">Verificados</option>
                    <option value="unverified">Não Verificados</option>
                </select>
            </div>
            <div>
                <label for="filterLoginType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de
                    Login:</label>
                <select wire:model.live="filterLoginType" id="filterLoginType"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Todos</option>
                    <option value="password">E-mail/Senha</option>
                    <option value="social">Login Social</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Tabela de Clientes --}}
    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Nome</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        E-mail</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Telefone</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        E-mail Verificado</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Status Conta</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Registrado Em</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($clients as $client)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="{{ $client->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $client->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->phone_number ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($client->hasVerifiedEmail())
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">Verificado</span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">Não
                                    Verificado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($client->is_active)
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">Ativa</span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">Inativa</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                        <div>Ações</div>
                                        <div class="ms-1"><svg class="fill-current h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg></div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.booking.create-for-client', $client)">
                                        Novo Agendamento
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.clients.show', $client->id)">Ver
                                        Detalhes</x-dropdown-link>
                                    @if ($client->is_active)
                                        <x-dropdown-link href="#" wire:click.prevent="toggleAccountStatus({{ $client->id }})"
                                            wire:confirm="Tem certeza que deseja DESATIVAR a conta deste cliente?">Desativar
                                            Conta</x-dropdown-link>
                                    @else
                                        <x-dropdown-link href="#" wire:click.prevent="toggleAccountStatus({{ $client->id }})"
                                            wire:confirm="Tem certeza que deseja ATIVAR a conta deste cliente?">Ativar
                                            Conta</x-dropdown-link>
                                    @endif
                                    @if (!$client->hasVerifiedEmail())
                                        <x-dropdown-link href="#" wire:click.prevent="markAsVerified({{ $client->id }})"
                                            wire:confirm="Tem certeza que deseja marcar o e-mail de {{ $client->name }} como VERIFICADO?">Verificar
                                            E-mail</x-dropdown-link>
                                    @endif
                                    @if ($client->password && $client->hasVerifiedEmail() && !$client->provider_id)
                                        <x-dropdown-link href="#" wire:click.prevent="sendPasswordResetLink({{ $client->id }})"
                                            wire:confirm="Tem certeza que deseja enviar um link de redefinição de senha para {{ $client->name }}?">Resetar
                                            Senha</x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                            @if (empty($search) && empty($filterAccountStatus) && empty($filterEmailVerified) && empty($filterLoginType))
                                Nenhum cliente encontrado.
                            @else
                                Nenhum cliente encontrado para os filtros e busca aplicados.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if ($clients->hasPages())
        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    @endif
</div>