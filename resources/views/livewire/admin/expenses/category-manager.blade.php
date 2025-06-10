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

    {{-- Formulário para Adicionar/Editar Categoria --}}
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow">
        <h3 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-3">
            {{ $categoryId ? 'Editar Categoria' : 'Adicionar Nova Categoria' }}
        </h3>
        <form wire:submit.prevent="saveCategory" class="space-y-4">
            <div>
                <x-input-label for="name" :value="__('Nome da Categoria')" />
                <x-text-input wire:model="name" id="name" type="text" class="mt-1 block w-full" />
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <x-input-label for="description" :value="__('Descrição (Opcional)')" />
                <textarea wire:model="description" id="description" rows="3"
                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-center space-x-4">
                <x-primary-button type="submit">
                    {{ $categoryId ? 'Atualizar Categoria' : 'Salvar Categoria' }}
                </x-primary-button>
                @if ($categoryId)
                    <x-secondary-button type="button" wire:click="resetForm">
                        Cancelar Edição
                    </x-secondary-button>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabela de Categorias Existentes --}}
    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-3">Categorias Existentes</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                    <th scope="col" class="relative px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $category->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="editCategory({{ $category->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 font-semibold">Editar</button>
                            <button wire:click="deleteCategory({{ $category->id }})" wire:confirm="Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita." class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 font-semibold ml-4">Excluir</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Nenhuma categoria encontrada. Adicione uma no formulário acima.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($categories->hasPages())
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    @endif
</div>