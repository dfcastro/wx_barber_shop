{{-- resources/views/livewire/admin/services/service-form.blade.php --}}
<div>
    <form wire:submit.prevent="saveService">
        @csrf {{-- Laravel Livewire lida com CSRF automaticamente, mas é boa prática em forms tradicionais --}}

        {{-- Campo Nome --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nome do Serviço</label>
            <input type="text" wire:model.defer="name" id="name"
                   class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Campo Preço --}}
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-700">Preço (R$)</label>
            <input type="number" step="0.01" wire:model.defer="price" id="price"
                   class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Campo Duração --}}
        <div class="mb-4">
            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duração (minutos)</label>
            <input type="number" wire:model.defer="duration_minutes" id="duration_minutes"
                   class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @error('duration_minutes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Salvar Serviço
            </button>
            <a href="{{ route('admin.services.index') }}" class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancelar
            </a>
        </div>
    </form>
</div>