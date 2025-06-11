<div>
    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Períodos Bloqueados</h1>
        <x-button.create href="{{ route('admin.blocked-periods.create') }}">Bloquear Novo Período</x-button.create>
    </div>

    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Início do Bloqueio</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fim do Bloqueio</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Motivo</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($blockedPeriods as $period)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="{{ $period->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($period->start_datetime)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($period->end_datetime)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $period->reason ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                        <div>Ações</div>
                                        <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('admin.blocked-periods.edit', $period->id) }}">Editar</x-dropdown-link>
                                    <x-dropdown-link href="#" wire:click.prevent="confirmPeriodDeletion({{ $period->id }})">Deletar</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">Nenhum período bloqueado encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($blockedPeriods->hasPages())
        <div class="mt-4">{{ $blockedPeriods->links() }}</div>
    @endif
    
    {{-- Modal de Confirmação Corrigido para usar eventos --}}
    <x-modal name="confirm-period-deletion" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Confirmar Exclusão</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Você tem certeza que deseja deletar este período bloqueado?</p>
            <div class="mt-6 flex justify-end">
                {{-- O botão de cancelar agora despacha o evento 'close' do Alpine.js --}}
                <x-secondary-button @click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>
                {{-- O botão de deletar fecha o modal e chama o método Livewire --}}
                <x-danger-button class="ms-3" wire:click="deletePeriod" @click="$dispatch('close')">
                    Deletar Período
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>