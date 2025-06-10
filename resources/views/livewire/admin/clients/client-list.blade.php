{{-- resources/views/livewire/admin/clients/client-list.blade.php --}}
<div>
    {{-- Mensagens Flash (incluindo para 'info') --}}
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

    {{-- Campo de Busca (como antes) --}}
    <div class="mb-4 md:flex md:justify-between md:items-center">
        <div class="mb-4 md:mb-0">
            <a href="{{ route('admin.clients.create') }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Novo Cliente
            </a>
        </div>
        <div class="w-full md:w-1/2 lg:w-1/3">
            <x-text-input wire:model.live.debounce.300ms="search" type="text" class="block w-full"
                placeholder="Buscar por nome, e-mail..." />
        </div>
    </div>
    {{-- SEÇÃO DE FILTROS --}}
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow">
        <h3 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-3">Filtrar Clientes:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Filtro Status da Conta (como antes) --}}
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
            {{-- Filtro E-mail Verificado (como antes) --}}
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
            {{-- NOVO FILTRO TIPO DE LOGIN --}}
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
    {{-- FIM DA SEÇÃO DE FILTROS --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    {{-- ... (Cabeçalhos das colunas como antes) ... --}}
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
                    <th scope="col"
                        class="relative px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($clients as $client)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{-- ... (Células das colunas como antes) ... --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $client->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->phone_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($client->hasVerifiedEmail())
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                    Verificado
                                </span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                    Não Verificado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($client->is_active)
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                    Ativa
                                </span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                    Inativa
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->created_at->format('d/m/Y') }}
                        </td>
                        {{-- Ações --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.clients.show', $client->id) }}"
                                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200"
                                    title="Ver Detalhes">
                                    Detalhes
                                </a>
                                {{-- Botão Ativar/Desativar --}}
                                @if ($client->is_active)
                                    <button wire:click="toggleAccountStatus({{ $client->id }})"
                                        wire:confirm="Tem certeza que deseja DESATIVAR a conta deste cliente? Ele não poderá mais fazer login."
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-semibold"
                                        title="Desativar Conta">
                                        Desativar
                                    </button>
                                @else
                                    <button wire:click="toggleAccountStatus({{ $client->id }})"
                                        wire:confirm="Tem certeza que deseja ATIVAR a conta deste cliente?"
                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 font-semibold"
                                        title="Ativar Conta">
                                        Ativar
                                    </button>
                                @endif

                                {{-- Botão Verificar E-mail --}}
                                @if (!$client->hasVerifiedEmail())
                                    <button wire:click="markAsVerified({{ $client->id }})"
                                        wire:confirm="Tem certeza que deseja marcar o e-mail de {{ $client->name }} como VERIFICADO?"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 font-semibold"
                                        title="Marcar E-mail como Verificado">
                                        Verificar E-mail
                                    </button>
                                @endif

                                {{-- NOVO BOTÃO: Enviar Link de Reset de Senha --}}
                                @if ($client->password && $client->hasVerifiedEmail() && !$client->provider_id) {{--
                                    Condições para mostrar o botão --}}
                                    <button wire:click="sendPasswordResetLink({{ $client->id }})"
                                        wire:confirm="Tem certeza que deseja enviar um link de redefinição de senha para {{ $client->name }} ({{ $client->email }})?"
                                        class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200 font-semibold"
                                        title="Enviar Link de Reset de Senha">
                                        Resetar Senha
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"
                            class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">
                            {{-- Mensagem atualizada para refletir busca e todos os filtros --}}
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

    @if ($clients->hasPages())
        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    @endif
</div>