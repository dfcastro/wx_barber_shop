{{-- resources/views/livewire/booking-process.blade.php --}}
<div>
    {{-- Mensagens de feedback da sessão --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 dark:text-red-300 dark:bg-red-900 dark:border-red-700 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif

  
  
    @if ($userHasReachedMaxAppointments) {{-- Se o usuário ATINGIU o limite --}}
        <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-300">
            <p class="font-bold">Atenção</p>
            {{-- A mensagem de erro específica virá da session()->flash() no método bookAppointment ou --}}
            {{-- pode ser uma mensagem genérica aqui se você preferir. --}}
            <p>Você atingiu o limite de agendamento(s) futuro(s) ativo(s) permitido(s). Para marcar um novo, por favor, cancele um existente ou aguarde sua realização.</p>
            <p class="mt-2"><a href="{{ route('client.appointments.index') }}" class="text-yellow-800 dark:text-yellow-200 hover:text-yellow-900 dark:hover:text-yellow-100 underline">Ver meus agendamentos</a></p>
        </div>
    @endif

    {{-- Desabilitar os campos se $userHasReachedMaxAppointments for true --}}
    <div class="mb-6 @if($userHasReachedMaxAppointments) opacity-50 pointer-events-none @endif">
    <label for="service" class="block text-sm font-medium text-gray-700 dark:text-gray-400">1. Escolha o Serviço:</label>
        <select wire:model.lazy="selectedServiceId" id="service" name="service"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                @if($userHasReachedMaxAppointments) disabled @endif>
            <option value="">Selecione um serviço...</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration_minutes }} min - R$ {{ number_format($service->price, 2, ',', '.') }})</option>
            @endforeach
        </select>
        @error('selectedServiceId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- ETAPA 2: Selecionar Data --}}
    @if ($selectedServiceId)
        <div class="mb-6 @if($userHasReachedMaxAppointments) opacity-50 pointer-events-none @endif">
            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-400">2. Escolha a Data:</label>
            <input type="date" wire:model.lazy="selectedDate" id="date" name="date"
                   min="{{ now()->format('Y-m-d') }}"
                   class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                   @if($userHasReachedMaxAppointments) disabled @endif>
            @error('selectedDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    @endif

    {{-- ETAPA 3: Selecionar Horário --}}
    @if ($selectedServiceId && $selectedDate)
        <div class="mb-6 @if($userHasReachedMaxAppointments) opacity-50 @endif">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-400">3. Escolha um Horário para {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') : '' }}:</h3>
            @if (!empty($availableTimeSlots))
                <div class="mt-2 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                    @foreach ($availableTimeSlots as $slot)
                        <button type="button" wire:click="selectTimeSlot('{{ $slot }}')"
                                class="py-2 px-3 border rounded-md text-sm font-medium
                                       {{ $selectedTimeSlot == $slot ? 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-500 dark:border-indigo-500' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200 border-gray-300 dark:border-gray-500' }}
                                       @if($userHasReachedMaxAppointments) opacity-50 pointer-events-none cursor-not-allowed @endif"
                                @if($userHasReachedMaxAppointments) disabled @endif>
                            {{ $slot }}
                        </button>
                    @endforeach
                </div>
            @elseif (!$userHasReachedMaxAppointments)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Nenhum horário disponível para esta data e serviço. Por favor, tente outra data ou serviço.
                </p>
            @endif
            <div wire:loading wire:target="selectedServiceId, selectedDate, loadAvailableTimeSlots" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Buscando horários...
            </div>
        </div>
    @endif

    {{-- ETAPA 4: Confirmar Agendamento --}}
    @if ($selectedServiceId && $selectedDate && $selectedTimeSlot)
        <div class="mt-8">
            <button wire:click="bookAppointment" type="button"
                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500
                           @if($userHasReachedMaxAppointments) opacity-50 cursor-not-allowed @endif"
                    @if($userHasReachedMaxAppointments) disabled @endif
                    wire:loading.attr="disabled" wire:loading.class="opacity-50">
                {{-- ... (conteúdo do botão com span para loading) ... --}}
                 <span wire:loading wire:target="bookAppointment" class="mr-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="bookAppointment">
                    Confirmar Agendamento para {{ $selectedTimeSlot }} em {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') : '' }}
                </span>
                <span wire:loading wire:target="bookAppointment">
                    Processando...
                </span>
            </button>
        </div>
    @endif

    {{-- </fieldset> --}} {{-- Fechar o fieldset se você o usou --}}

    {{-- Seção de Debug (mantenha comentada ou remova para produção) --}}
    {{--
    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600">
        <h4 class="font-bold text-lg text-gray-800 dark:text-gray-200">Debug Info:</h4>
        <p class="text-sm text-gray-700 dark:text-gray-300">User Has Active Upcoming Appointment: <strong>{{ $userHasActiveUpcomingAppointment ? 'Sim' : 'Não' }}</strong></p>
        <p class="text-sm text-gray-700 dark:text-gray-300">Selected Service ID: <strong>{{ $selectedServiceId ?? 'Nenhum' }}</strong></p>
        <p class="text-sm text-gray-700 dark:text-gray-300">Selected Date: <strong>{{ $selectedDate ?? 'Nenhuma' }}</strong></p>
        <p class="text-sm text-gray-700 dark:text-gray-300">Available Slots: <pre class="whitespace-pre-wrap">{{ var_export($availableTimeSlots, true) }}</pre></p>
        <p class="text-sm text-gray-700 dark:text-gray-300">Selected Slot: <strong>{{ $selectedTimeSlot ?? 'Nenhum' }}</strong></p>
    </div>
    --}}
</div>