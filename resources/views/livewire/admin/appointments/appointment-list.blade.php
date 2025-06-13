<div>
    {{-- Mensagens de Feedback --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filtros e Pesquisa --}}
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="search" :value="__('Buscar por Cliente/Serviço')" />
                <x-text-input wire:model.live.debounce.300ms="search" id="search" class="block mt-1 w-full" type="text" name="search" />
            </div>
            <div>
                <x-input-label for="filterStatus" :value="__('Filtrar por Status')" />
                <select wire:model.live="filterStatus" id="filterStatus" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">Todos os Status</option>
                    <option value="pendente">Pendente</option>
                    <option value="confirmado">Confirmado</option>
                    <option value="concluido">Concluído</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div>
                <x-input-label for="filterDate" :value="__('Filtrar por Data')" />
                <x-text-input wire:model.live.debounce.300ms="filterDate" id="filterDate" class="block mt-1 w-full" type="date" name="filterDate" />
            </div>
        </div>
    </div>

    {{-- Tabela de Agendamentos --}}
    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Serviço</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data e Hora</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($appointments as $appointment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="{{ $appointment->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->user?->name ?? 'Cliente Removido' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $appointment->service?->name ?? 'Serviço Removido' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            @if($appointment->appointment_time)
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                            @else
                                Data/Hora Inválida
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @switch($appointment->status)
                                    @case('pendente') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 @break
                                    @case('confirmado') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                    @case('concluido') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                    @case('cancelado') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100 @break
                                @endswitch
                            ">
                                {{ __(ucfirst($appointment->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                             {{-- O botão de Ações só aparece se o status não for 'concluido' ou 'cancelado' --}}
                             @if(!in_array($appointment->status, ['concluido', 'cancelado']))
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                            <div>Ações</div>
                                            <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        {{-- Ação: APROVAR um agendamento pendente --}}
                                        @if($appointment->status === 'pendente')
                                            <x-dropdown-link href="#" wire:click.prevent="updateStatus({{ $appointment->id }}, 'confirmado')">
                                                Aprovar Agendamento
                                            </x-dropdown-link>
                                        @endif

                                        {{-- Ação: MARCAR COMO CONCLUÍDO um agendamento confirmado --}}
                                        @if($appointment->status === 'confirmado')
                                             <x-dropdown-link href="#" wire:click.prevent="updateStatus({{ $appointment->id }}, 'concluido')">
                                                Marcar como Concluído
                                             </x-dropdown-link>
                                        @endif

                                        {{-- Ação: CANCELAR um agendamento (seja pendente ou confirmado) --}}
                                        <x-dropdown-link href="#" wire:click.prevent="updateStatus({{ $appointment->id }}, 'cancelado')" class="text-red-600 hover:bg-red-50 dark:hover:bg-red-900">
                                            Cancelar Agendamento
                                        </x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                             @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">Nenhum agendamento encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if ($appointments->hasPages())
        <div class="mt-4">{{ $appointments->links() }}</div>
    @endif
</div>