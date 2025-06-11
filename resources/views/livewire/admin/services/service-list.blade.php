<div>
    {{-- Mensagens de Feedback --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Cabeçalho: Título e Botão Adicionar --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Lista de Serviços
        </h1>
        <a href="{{ route('admin.services.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Novo Serviço
        </a>
    </div>

    {{-- Filtros e Pesquisa --}}
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="search" :value="__('Buscar por Nome ou Descrição')" />
                <x-text-input wire:model.live.debounce.300ms="search" id="search" class="block mt-1 w-full" type="text" name="search" />
            </div>
            <div>
                <x-input-label for="filterStatus" :value="__('Filtrar por Status')" />
                <select wire:model.live="filterStatus" id="filterStatus" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">Todos</option>
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Tabela de Serviços --}}
    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Preço</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duração (min)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($services as $service)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="{{ $service->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $service->duration_minutes }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($service->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">Ativo</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">Inativo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                        <div>Ações</div>
                                        <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.services.edit', $service->id)">
                                        Editar
                                    </x-dropdown-link>
                                    <x-dropdown-link href="#" wire:click.prevent="confirmServiceDeletion({{ $service->id }})">
                                        Deletar
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                            Nenhum serviço encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if ($services->hasPages())
        <div class="mt-4">{{ $services->links() }}</div>
    @endif
    
    {{-- Modal de Confirmação de Exclusão --}}
    <x-modal name="confirm-service-deletion" :show="$showDeleteModal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Confirmar Exclusão
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Você tem certeza que deseja deletar este serviço? Esta ação não pode ser desfeita.
            </p>
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                    Cancelar
                </x-secondary-button>
                <x-danger-button class="ms-3" wire:click="deleteService" wire:loading.attr="disabled">
                    Deletar Serviço
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>