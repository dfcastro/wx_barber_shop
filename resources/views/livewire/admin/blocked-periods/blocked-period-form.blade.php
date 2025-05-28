{{-- resources/views/livewire/admin/blocked-periods/blocked-period-form.blade.php --}}
<div>
    <form wire:submit.prevent="saveBlockedPeriod">
        @csrf

        {{-- Data/Hora de Início --}}
        <div class="mb-4">
            <label for="start_datetime" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Início do Bloqueio</label>
            <input type="datetime-local" wire:model.lazy="start_datetime" id="start_datetime"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            @error('start_datetime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Data/Hora de Fim --}}
        <div class="mb-4">
            <label for="end_datetime" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fim do Bloqueio</label>
            <input type="datetime-local" wire:model.lazy="end_datetime" id="end_datetime"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            @error('end_datetime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Motivo --}}
        <div class="mb-4">
            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo (Opcional)</label>
            <textarea wire:model.lazy="reason" id="reason" rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"></textarea>
            @error('reason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <a href="{{ route('admin.blocked-periods.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline mr-4">
                Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ $blockedPeriodInstance ? 'Atualizar Bloqueio' : 'Adicionar Bloqueio' }}
            </button>
        </div>
    </form>
</div>