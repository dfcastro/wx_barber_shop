{{-- resources/views/livewire/admin/services/service-list.blade.php --}}
<div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nome
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Preço
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Duração (min)
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Ações</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($services as $service)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $service->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    R$ {{ number_format($service->price, 2, ',', '.') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $service->duration_minutes }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.services.edit', $service) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                    {{-- Ação de deletar virá depois, geralmente com um formulário e confirmação --}}
                    <button
                        wire:click="deleteService({{ $service->id }})"
                        wire:confirm="Tem certeza que deseja excluir o serviço '{{ $service->name }}'?"
                        class="text-red-600 hover:text-red-900">
                        Deletar
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    Nenhum serviço cadastrado.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{-- Modal de confirmação de exclusão (vamos adicionar depois, se necessário) --}}
</div>