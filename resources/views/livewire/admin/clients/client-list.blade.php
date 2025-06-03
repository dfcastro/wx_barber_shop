{{-- resources/views/livewire/admin/clients/client-list.blade.php --}}
<div>
    {{-- Mensagem Flash --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 dark:text-red-300 dark:bg-red-900 dark:border-red-700 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif


    {{-- Campo de Busca --}}
    <div class="mb-4">
        <x-text-input
            wire:model.live.debounce.300ms="search"
            type="text"
            class="block w-full sm:w-2/3 md:w-1/2"
            placeholder="Buscar por nome, e-mail..." />
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">E-mail</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Telefone</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Login Social</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th> {{-- << NOVA COLUNA --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registrado Em</th>
                    <th scope="col" class="relative px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th> {{-- << COLUNA DE AÇÕES ATUALIZADA --}}
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($clients as $client)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $client->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->phone_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            @if ($client->provider_name)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100">
                                    {{ ucfirst($client->provider_name) }}
                                </span>
                            @else
                                E-mail/Senha
                            @endif
                        </td>
                        {{-- Status da Conta --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($client->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                    Ativa
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                    Inativa
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $client->created_at->format('d/m/Y') }}
                        </td>
                        {{-- Ações --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.clients.show', $client->id) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 mr-3">
                                Detalhes
                            </a>
                            @if ($client->is_active)
                                <button wire:click="toggleAccountStatus({{ $client->id }})"
                                        wire:confirm="Tem certeza que deseja DESATIVAR a conta deste cliente? Ele não poderá mais fazer login."
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-semibold">
                                    Desativar
                                </button>
                            @else
                                <button wire:click="toggleAccountStatus({{ $client->id }})"
                                        wire:confirm="Tem certeza que deseja ATIVAR a conta deste cliente?"
                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 font-semibold">
                                    Ativar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">
                            @if (empty($search))
                                Nenhum cliente encontrado.
                            @else
                                Nenhum cliente encontrado para "{{ $search }}".
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