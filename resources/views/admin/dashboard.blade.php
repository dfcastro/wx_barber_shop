<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-card>
            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Ganhos Totais</h3>
            <p class="text-3xl font-bold mt-2">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total de Agendamentos</h3>
            <p class="text-3xl font-bold mt-2">{{ $totalAppointments }}</p>
        </x-card>
        <x-card>
            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Novos Clientes (Mês)</h3>
            <p class="text-3xl font-bold mt-2">{{ $newClientsThisMonth }}</p>
        </x-card>
        <x-card>
            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Total de Despesas</h3>
            <p class="text-3xl font-bold mt-2">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</p>
        </x-card>
    </div>

    <x-card>
        <h3 class="text-xl font-semibold mb-4">Próximos Agendamentos</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 dark:border-gray-600 text-left text-sm leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 dark:border-gray-600 text-left text-sm leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Serviço</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 dark:border-gray-600 text-left text-sm leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data e Hora</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @forelse ($upcomingAppointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700">{{ $appointment->user->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700">{{ $appointment->service->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 dark:border-gray-700 text-center">Nenhum agendamento próximo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>