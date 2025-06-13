<div>
    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        
        <x-button.create href="{{ route('admin.services.create') }}">Novo Serviço</x-button.create>
    </div>

    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow">
        <div>
            <x-input-label for="search" :value="__('Buscar por Nome ou Descrição')" />
            <x-text-input wire:model.live.debounce.300ms="search" id="search" class="block mt-1 w-full" type="text" name="search" />
        </div>
    </div>

    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
             <thead class="bg-gray-200 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Preço</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duração (min)</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($services as $service)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="{{ $service->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $service->duration_minutes }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                        <div>Ações</div>
                                        <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.services.edit', $service->id)">Editar</x-dropdown-link>
                                    <x-dropdown-link href="#" wire:click.prevent="confirmServiceDeletion({{ $service->id }})">Deletar</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">Nenhum serviço encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($services->hasPages())
        <div class="mt-4">{{ $services->links() }}</div>
    @endif
    
    <div
        x-data="{ show: false }"
        x-show="show"
        x-on:open-delete-modal.window="show = true" {{-- Ouve o evento do Livewire --}}
        x-on:keydown.escape.window="show = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-500/75" @click="show = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="show" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Confirmar Exclusão</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Você tem certeza que deseja deletar este serviço? Esta ação não pode ser desfeita.</p>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button @click="show = false"> {{-- Fecha o modal via Alpine --}}
                        Cancelar
                    </x-secondary-button>
                    <x-danger-button class="ms-3" wire:click="deleteService" @click="show = false"> {{-- Fecha o modal e chama o método Livewire --}}
                        Deletar Serviço
                    </x-danger-button>
                </div>
            </div>
        </div>
    </div>
    </div>