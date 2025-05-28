{{-- resources/views/livewire/client/my-appointments-list.blade.php --}}
<div>
    {{-- ... (mensagens de session success/error no topo) ... --}}
     @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error') || $cancellationNotAllowedReason)
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 dark:text-red-300 dark:bg-red-900 dark:border-red-700 rounded" role="alert">
            {{ session('error') ?: $cancellationNotAllowedReason }}
        </div>
    @endif


    {{-- Próximos Agendamentos --}}
    <section class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Próximos Agendamentos</h3>
        @if ($upcomingAppointments && $upcomingAppointments->count() > 0)
            <div class="overflow-x-auto bg-white dark:bg-gray-700 shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    {{-- ... (thead continua o mesmo) ... --}}
                     <thead class="bg-gray-50 dark:bg-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Serviço</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data / Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($upcomingAppointments as $appointment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $appointment->service->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($appointment->status == 'pendente') bg-yellow-100 text-yellow-800 @endif
                                        @if($appointment->status == 'confirmado') bg-green-100 text-green-800 @endif
                                    ">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{-- Botão Cancelar --}}
                                    @if($appointment->status == 'pendente' || $appointment->status == 'confirmado')
                                        @php
                                            $canCancel = \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($appointment->appointment_time), false) >= self::MIN_HOURS_BEFORE_CANCELLATION;
                                        @endphp
                                        @if ($canCancel)
                                        <button wire:click="confirmCancellation({{ $appointment->id }})"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                            Cancelar
                                        </button>
                                        @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Cancelamento não disponível</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">Você não possui agendamentos futuros.</p>
        @endif
    </section>

    {{-- ... (Seção Histórico de Agendamentos continua a mesma) ... --}}

    {{-- Modal de Confirmação de Cancelamento --}}
    @if ($appointmentToCancel)
    <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-opacity-60" aria-hidden="true" wire:click="closeModal()"></div>
            <div class="inline-block bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-200 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Cancelar Agendamento
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-300">
                                    Tem certeza que deseja cancelar seu agendamento para o serviço "<strong>{{ $appointmentToCancel->service->name }}</strong>" em <strong>{{ \Carbon\Carbon::parse($appointmentToCancel->appointment_time)->format('d/m/Y \à\s H:i') }}</strong>?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="cancelAppointment()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Sim, Cancelar
                    </button>
                    <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 dark:text-gray-200 text-base font-medium text-gray-700 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Não, Manter Agendamento
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>