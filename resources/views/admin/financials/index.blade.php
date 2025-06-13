<x-app-layout>
    <div class="space-y-8">
        {{-- Cabeçalho com Título e Filtro de Data --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
                Relatório Financeiro
            </h1>
            <x-card>
                <form action="{{ route('admin.financials.index') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <x-input-label for="start_date" :value="__('Data de Início')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="request('start_date')" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Data de Fim')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="request('end_date')" />
                        </div>
                        <div class="flex space-x-2">
                            <x-primary-button>
                                Filtrar
                            </x-primary-button>
                            <x-secondary-button as="a" href="{{ route('admin.financials.index') }}">
                                Limpar
                            </x-secondary-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Cards de Resumo --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-card>
                <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Ganhos Totais</h3>
                <p class="text-3xl font-bold mt-2 text-green-600 dark:text-green-400">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
            </x-card>
            <x-card>
                <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Despesas Totais</h3>
                <p class="text-3xl font-bold mt-2 text-red-600 dark:text-red-400">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</p>
            </x-card>
            <x-card>
                <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Lucro Líquido</h3>
                <p class="text-3xl font-bold mt-2 text-blue-600 dark:text-blue-400">R$ {{ number_format($netProfit, 2, ',', '.') }}</p>
            </x-card>
        </div>

        {{-- Detalhes em Tabelas --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Tabela de Últimos Agendamentos Pagos --}}
            <x-card>
                <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Agendamentos Pagos no Período</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($paidAppointments as $appointment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $appointment->appointment_time->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">R$ {{ number_format($appointment->service->price, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">Nenhum agendamento pago no período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Tabela de Últimas Despesas --}}
            <x-card>
                 <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Despesas no Período</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                           @forelse ($expenses as $expense)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $expense->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">R$ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">Nenhuma despesa no período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>