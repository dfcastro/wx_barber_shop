<div>
    {{-- Mensagens Flash --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded" role="alert">
            {{ session('message') }}
        </div>
    @endif

    {{-- Formulário para Adicionar/Editar Despesa --}}
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow">
        <h3 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-3">
            {{ $expenseId ? 'Editar Despesa' : 'Lançar Nova Despesa' }}
        </h3>
        <form wire:submit.prevent="saveExpense" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Categoria --}}
                <div>
                    <x-input-label for="expense_category_id" :value="__('Categoria')" />
                    <select wire:model="expense_category_id" id="expense_category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">Selecione...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('expense_category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                {{-- Descrição --}}
                <div>
                    <x-input-label for="description" :value="__('Descrição')" />
                    <x-text-input wire:model="description" id="description" type="text" class="mt-1 block w-full" />
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                {{-- Valor --}}
                <div>
                    <x-input-label for="amount" :value="__('Valor (R$)')" />
                    <x-text-input wire:model="amount" id="amount" type="number" step="0.01" class="mt-1 block w-full" />
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                {{-- Data da Despesa --}}
                <div>
                    <x-input-label for="expense_date" :value="__('Data da Despesa')" />
                    <x-text-input wire:model="expense_date" id="expense_date" type="date" class="mt-1 block w-full" />
                    @error('expense_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            {{-- Notas --}}
            <div>
                <x-input-label for="notes" :value="__('Observações (Opcional)')" />
                <textarea wire:model="notes" id="notes" rows="3"
                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-center space-x-4">
                <x-primary-button type="submit">
                    {{ $expenseId ? 'Atualizar Despesa' : 'Salvar Despesa' }}
                </x-primary-button>
                @if ($expenseId)
                    <x-secondary-button type="button" wire:click="resetForm">
                        Cancelar Edição
                    </x-secondary-button>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabela de Despesas Lançadas --}}
    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-3">Histórico de Despesas</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoria</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                    <th scope="col" class="relative px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($expenses as $expense)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $expense->expense_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $expense->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $expense->category->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-red-600 dark:text-red-400">- R$ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="editExpense({{ $expense->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 font-semibold">Editar</button>
                            <button wire:click="deleteExpense({{ $expense->id }})" wire:confirm="Tem certeza que deseja excluir esta despesa? Esta ação não pode ser desfeita." class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 font-semibold ml-4">Excluir</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Nenhuma despesa lançada ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($expenses->hasPages())
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    @endif
</div>