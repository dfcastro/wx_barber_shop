<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard do Administrador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- SEÇÃO DE CARDS DE ESTATÍSTICAS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Agendamentos Hoje</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $appointmentsTodayCount }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Receita do Mês</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($revenueThisMonth, 2, ',', '.') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total de Clientes</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalClientsCount }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Novos Clientes (30 dias)</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $newClientsCount }}</p>
                </div>
            </div>

            {{-- SEÇÃO PRÓXIMOS AGENDAMENTOS --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Próximos Agendamentos
                    </h3>
                    <div class="overflow-x-auto">
                        @if ($upcomingAppointments->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Serviço</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data / Hora</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($upcomingAppointments as $appointment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->user->name ?? 'Cliente não encontrado' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $appointment->service->name ?? 'Serviço não encontrado' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum agendamento próximo.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>