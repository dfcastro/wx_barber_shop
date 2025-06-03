{{-- resources/views/admin/clients/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalhes do Cliente: ') }} {{ $client->name }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.clients.edit', $client) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    {{ __('Editar Cliente') }}
                </a>
                {{-- NOVO BOTÃO DE AGENDAMENTO --}}
                <a href="{{ route('admin.booking.create-for-client', $client) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('Agendar para este Cliente') }}
                </a>
                <a href="{{ route('admin.clients.index') }}"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">
                    &larr; {{ __('Voltar para a Lista de Clientes') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">

                    {{-- Informações Básicas --}}
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Informações do Perfil
                        </h3>
                        <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome Completo</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Endereço de E-mail</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->email }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número de Telefone</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $client->phone_number ?? 'Não informado' }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Registro</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $client->created_at->format('d/m/Y H:i:s') }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail Verificado</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if ($client->email_verified_at)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                            Sim ({{ $client->email_verified_at->format('d/m/Y') }})
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                            Não
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            @if ($client->provider_name)
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Login Social</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ ucfirst($client->provider_name) }} (ID: {{ $client->provider_id }})
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- SEÇÃO DE HISTÓRICO DE AGENDAMENTOS --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                Histórico de Agendamentos ({{ $client->appointments->count() }})
                            </h3>
                            @if ($client->appointments->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Data/Hora</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Serviço</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Status</th>
                                                {{-- Poderia adicionar uma coluna para o admin gerenciar o agendamento
                                                diretamente daqui --}}
                                                {{-- <th scope="col" class="relative px-6 py-3"><span
                                                        class="sr-only">Ações</span></th> --}}
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($client->appointments as $appointment)
                                                <tr>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                                        {{ Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                                        {{ $appointment->service->name ?? 'Serviço não encontrado' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        @if ($appointment->status === 'pendente')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                                                Pendente
                                                            </span>
                                                        @elseif ($appointment->status === 'confirmado')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                                                Confirmado
                                                            </span>
                                                        @elseif ($appointment->status === 'cancelado')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                                                Cancelado
                                                            </span>
                                                        @elseif ($appointment->status === 'concluido')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100">
                                                                Concluído
                                                            </span>
                                                        @else
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                                                {{ ucfirst($appointment->status) }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    {{-- <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="{{ route('admin.appointments.edit', $appointment->id) }}"
                                                            class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{-- Se você implementar paginação para os agendamentos aqui: --}}
                                 {{--   <div class="mt-4"> {{ $client->appointments()->paginate(5)->links() }} </div>--}}
                            @else
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Nenhum agendamento encontrado para
                                    este cliente.</p>
                            @endif
                        </div>
                    </div>
                    {{-- ... (Bloco de "Ações de Agendamento" com o botão para o admin agendar para o cliente, como
                    antes) ... --}}
                </div>
            </div>
</x-app-layout>