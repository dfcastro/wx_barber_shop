<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Relatório Financeiro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Resumo do Mês Atual --}}
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4 px-3 sm:px-0">Resumo do Mês Atual</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 flex flex-col justify-between">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Faturamento do Mês</p>
                    <p class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400">R$ {{ number_format($revenueThisMonth, 2, ',', '.') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 flex flex-col justify-between">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Despesas do Mês</p>
                    <p class="mt-1 text-3xl font-semibold text-red-600 dark:text-red-400">- R$ {{ number_format($expensesThisMonth, 2, ',', '.') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 flex flex-col justify-between">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Lucro Líquido do Mês</p>
                    <p class="mt-1 text-3xl font-semibold {{ $profitThisMonth >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                        R$ {{ number_format($profitThisMonth, 2, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Históricos de Receitas e Despesas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Coluna de Receitas --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Últimas Receitas (Agendamentos Pagos)
                        </h3>
                        <div class="overflow-x-auto">
                            @if ($paidTransactions->isNotEmpty())
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($paidTransactions as $transaction)
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($transaction->appointment_time)->format('d/m/y') }}</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->user->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400 font-semibold">+ R$ {{ number_format($transaction->service->price ?? 0, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma receita encontrada.</p>
                            @endif
                        </div>
                        @if ($paidTransactions->hasPages())
                            <div class="mt-4">
                                {{ $paidTransactions->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Coluna de Despesas --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Últimas Despesas Lançadas
                        </h3>
                        <div class="overflow-x-auto">
                            @if ($recentExpenses->isNotEmpty())
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($recentExpenses as $expense)
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $expense->expense_date->format('d/m/y') }}</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $expense->description }}</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400 font-semibold">- R$ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma despesa lançada.</p>
                            @endif
                        </div>
                        @if ($recentExpenses->hasPages())
                            <div class="mt-4">
                                {{ $recentExpenses->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>