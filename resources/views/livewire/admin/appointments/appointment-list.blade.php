{{-- resources/views/livewire/admin/appointments/appointment-list.blade.php --}}
<div>
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Seção de Filtros --}}
    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <div>
            <label for="filterDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Data:</label>
            <input type="date" wire:model.lazy="filterDate" id="filterDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
        </div>
        <div>
            <label for="filterStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Status:</label>
            <select wire:model.lazy="filterStatus" id="filterStatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                <option value="">Todos</option>
                <option value="pendente">Pendente</option>
                <option value="confirmado">Confirmado</option>
                <option value="cancelado">Cancelado</option>
                <option value="concluido">Concluído</option>
            </select>
        </div>
        <div>
            <label for="searchClient" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar Cliente (Nome/Email):</label>
            <input type="text" wire:model.debounce.500ms="searchClient" id="searchClient" placeholder="Digite nome ou email..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Serviço</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data / Hora</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($appointments as $appointment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $appointment->user->name ?? 'Usuário não encontrado' }}<br>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->user->email ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $appointment->service->name ?? 'Serviço não encontrado' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($appointment->status == 'pendente') bg-yellow-100 text-yellow-800 @endif
                                @if($appointment->status == 'confirmado') bg-green-100 text-green-800 @endif
                                @if($appointment->status == 'cancelado') bg-red-100 text-red-800 @endif
                                @if($appointment->status == 'concluido') bg-blue-100 text-blue-800 @endif
                            ">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if ($appointment->status == 'pendente')
                                <button wire:click="approveAppointment({{ $appointment->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-2">Aprovar</button>
                            @endif
                            @if ($appointment->status == 'pendente' || $appointment->status == 'confirmado')
                                <button wire:click="cancelAppointment({{ $appointment->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Cancelar</button>
                            @endif
                            {{-- Adicionar botão para "Concluir" se necessário --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">Nenhum agendamento encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $appointments->links() }} {{-- Links da paginação --}}
    </div>
</div>