<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Relatório Financeiro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- SEÇÃO DE CARDS DE ESTATÍSTICAS FINANCEIRAS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Faturamento Hoje</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($revenueToday, 2, ',', '.') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Faturamento da Semana</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($revenueThisWeek, 2, ',', '.') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Faturamento do Mês</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($revenueThisMonth, 2, ',', '.') }}</p>
                </div>
            </div>

            {{-- SEÇÃO DE TRANSAÇÕES PAGAS --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Histórico de Transações Pagas
                    </h3>
                    <div class="overflow-x-auto">
                        @if ($paidTransactions->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data do Agendamento</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Serviço</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor (R$)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($paidTransactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($transaction->appointment_time)->format('d/m/Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->user->name ?? 'Cliente não encontrado' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $transaction->service->name ?? 'Serviço não encontrado' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-200">{{ number_format($transaction->service->price ?? 0, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma transação paga encontrada.</p>
                        @endif
                    </div>

                    {{-- Links de Paginação --}}
                    @if ($paidTransactions->hasPages())
                        <div class="mt-4">
                            {{ $paidTransactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>