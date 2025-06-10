{{-- resources/views/livewire/admin/appointments/appointment-list.blade.php --}}
<div>
    {{-- Mensagens Flash --}}
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

    {{-- Filtro de Data (como antes) --}}
    <div class="mb-4">
        <label for="filterDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Data:</label>
        <x-text-input wire:model.live="filterDate" id="filterDate" type="date" class="mt-1 block w-full sm:w-1/3" />
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Serviço</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data / Hora</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pagamento</th> {{-- << NOVA COLUNA --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($appointments as $appointment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{-- ... (células Cliente, Serviço, Data/Hora, Status como antes) ... --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->user->name ?? 'Cliente Removido' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $appointment->service->name ?? 'Serviço Removido' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @switch($appointment->status)
                                    @case('pendente') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 @break
                                    @case('confirmado') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                    @case('cancelado') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100 @break
                                    @case('concluido') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                    @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 @break
                                @endswitch
                            ">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        
                        {{-- Célula Status do Pagamento --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($appointment->payment_status === 'pago')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                    Pago
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                    Pendente
                                </span>
                            @endif
                        </td>

                        {{-- Célula Ações --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if($appointment->status === 'pendente')
                                    <button wire:click="confirmAppointment({{ $appointment->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200 font-semibold" title="Confirmar Agendamento">Confirmar</button>
                                    <button wire:click="cancelAppointmentAsAdmin({{ $appointment->id }})" wire:confirm="Tem certeza que deseja CANCELAR este agendamento?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 font-semibold" title="Cancelar Agendamento">Cancelar</button>
                                @endif

                                {{-- NOVO BOTÃO: MARCAR COMO PAGO --}}
                                @if($appointment->status === 'confirmado' && $appointment->payment_status === 'pendente')
                                    <button wire:click="markAsPaid({{ $appointment->id }})"
                                            wire:confirm="Tem certeza que deseja marcar este agendamento como PAGO e CONCLUÍDO?"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 font-semibold" title="Marcar como Pago e Concluir">
                                        Marcar como Pago
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum agendamento encontrado para esta data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $appointments->links() }}
    </div>
</div>