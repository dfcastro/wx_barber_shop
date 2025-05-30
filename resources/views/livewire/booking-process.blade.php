{{-- resources/views/livewire/booking-process.blade.php --}}
<div>
    {{-- ... (mensagens de feedback e seleção de serviço como antes) ... --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 dark:text-green-300 dark:bg-green-900 dark:border-green-700 rounded"
            role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 dark:text-red-300 dark:bg-red-900 dark:border-red-700 rounded"
            role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($userHasReachedMaxAppointments)
        <div
            class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-300">
            <p class="font-bold">Atenção</p>
            <p>Você atingiu o limite de agendamento(s) futuro(s) ativo(s) permitido(s). Para marcar um novo, por favor,
                cancele um existente ou aguarde sua realização.</p>
            <p class="mt-2"><a href="{{ route('client.appointments.index') }}"
                    class="text-yellow-800 dark:text-yellow-200 hover:text-yellow-900 dark:hover:text-yellow-100 underline">Ver
                    meus agendamentos</a></p>
        </div>
    @endif

    {{-- ETAPA 1: Escolha o Serviço --}}
    <div class="mb-6 @if($userHasReachedMaxAppointments) opacity-50 pointer-events-none @endif">
        <label for="service" class="block text-sm font-medium text-gray-700 dark:text-gray-400">1. Escolha o
            Serviço:</label>
        <select wire:model.lazy="selectedServiceId" id="service" name="service"
            class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            wire:loading.attr="disabled" wire:target="selectedServiceId, loadAvailableTimeSlots"
            @if($userHasReachedMaxAppointments) disabled @endif>
            <option value="">Selecione um serviço...</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration_minutes }} min - R$
                    {{ number_format($service->price, 2, ',', '.') }})
                </option>
            @endforeach
        </select>
        @error('selectedServiceId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- resources/views/livewire/booking-process.blade.php --}}
    <div>
        {{-- ... (mensagens de feedback e seleção de serviço como antes) ... --}}

        {{-- ETAPA 2: Selecionar Data com Calendário Interativo --}}
        @if ($selectedServiceId)
            <div class="mb-6 @if($userHasReachedMaxAppointments) opacity-50 pointer-events-none @endif" wire:ignore {{-- <<<
                ADICIONADO AQUI: wire:ignore --}} x-data="{
                     datepickerInstance: null, // Para guardar a instância do Flatpickr se precisar destruí-la
                     initFlatpickr() {
                         // Se uma instância já existir (improvável com wire:ignore, mas bom para robustez)
                         if (this.datepickerInstance) {
                             this.datepickerInstance.destroy();
                         }
                         this.datepickerInstance = flatpickr(this.$refs.datepicker, {
                             altInput: true,
                             altFormat: 'd/m/Y',    // Formato de EXIBIÇÃO PT-BR
                             dateFormat: 'Y-m-d',   // Formato do VALOR INTERNO para Livewire
                             minDate: 'today',
                             defaultDate: this.$wire.get('selectedDate'),
                             locale: 'pt',
                             disable: [
                                 function(date) {
                                     // Desabilitar Domingos (0) e Segundas (1)
                                     return (date.getDay() === 0 || date.getDay() === 1);
                                 }
                             ],
                             onChange: (selectedDates, dateStr, instance) => {
                                 this.$wire.set('selectedDate', dateStr);
                             },
                             onClose: (selectedDates, dateStr, instance) => {
                                 // Pode reativar esta linha se o problema do campo selecionável voltar
                                 // e se não causar o problema de não conseguir reabrir.
                                 if (instance.altInput) {
                                     instance.altInput.blur();
                                 }
                             }
                         });
                     }
                 }" x-init="initFlatpickr()">
                <label for="date-flatpickr" class="block text-sm font-medium text-gray-700 dark:text-gray-400">2. Escolha a
                    Data:</label>
                <input x-ref="datepicker" type="text" id="date-flatpickr"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="DD/MM/AAAA" wire:loading.attr="disabled" wire:target="selectedDate, loadAvailableTimeSlots"
                    @if($userHasReachedMaxAppointments) disabled @endif />
                @error('selectedDate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        @endif

        {{-- ETAPA 3: Selecionar Horário (como na sua versão anterior com o spinner melhorado) --}}
        @if ($selectedServiceId && $selectedDate)
            <div class="mb-6 @if($userHasReachedMaxAppointments) opacity-50 @endif">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-400">3. Escolha um Horário para
                    {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') : '' }}:
                </h3>

                <div wire:loading wire:target="loadAvailableTimeSlots, selectedDate, selectedServiceId"
                    class="mt-2 text-center py-4">
                    <svg class="animate-spin h-8 w-8 text-indigo-600 dark:text-indigo-400 mx-auto"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buscando horários...</p>
                </div>

                <div wire:loading.remove wire:target="loadAvailableTimeSlots, selectedDate, selectedServiceId">
                    @if (!empty($availableTimeSlots))
                        <div class="mt-2 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                            @foreach ($availableTimeSlots as $slot)
                                    <button type="button" wire:click="selectTimeSlot('{{ $slot }}')" class="py-2 px-3 border rounded-md text-sm font-medium transition-colors duration-150
                                                                       {{ $selectedTimeSlot == $slot
                                ? 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-500 dark:border-indigo-500 ring-2 ring-indigo-500 ring-offset-1 dark:ring-offset-gray-800'
                                : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200 border-gray-300 dark:border-gray-500' }}
                                                                       @if($userHasReachedMaxAppointments || ($this->getPropertyValue('selectedServiceId') && $this->getPropertyValue('selectedDate') && $this->selectedTimeSlot && $this->selectedTimeSlot != $slot)) 
                                                                        opacity-75 cursor-not-allowed
                                                                       @endif
                                                                       " @if($userHasReachedMaxAppointments || ($this->getPropertyValue('selectedServiceId') && $this->getPropertyValue('selectedDate') && $this->selectedTimeSlot && $this->selectedTimeSlot != $slot)) disabled @endif
                                        wire:loading.attr="disabled" wire:target="selectTimeSlot, bookAppointment">
                                        {{ $slot }}
                                    </button>
                            @endforeach
                        </div>
                    @elseif (!$userHasReachedMaxAppointments)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Nenhum horário disponível para esta data e serviço. Por favor, tente outra data ou serviço.
                        </p>
                    @endif
                </div>
            </div>
        @endif

        {{-- ETAPA 4: Sumário e Confirmação do Agendamento (como na sua versão anterior) --}}
        @if ($selectedServiceId && $selectedService && $selectedDate && $selectedTimeSlot && !$userHasReachedMaxAppointments)
        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow">
            <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Confirme seu Agendamento:</h3>
            <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <p><strong>Serviço:</strong> {{ $selectedService->name }}</p>
                <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</p>
                <p><strong>Horário:</strong> {{ $selectedTimeSlot }}</p>
                <p><strong>Duração Estimada:</strong> {{ $selectedService->duration_minutes }} minutos</p>
                <p><strong>Preço:</strong> R$ {{ number_format($selectedService->price, 2, ',', '.') }}</p>
            </div>
            <div class="mt-6">
                <button wire:click="bookAppointment" type="button" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500
                           @if($userHasReachedMaxAppointments) opacity-50 cursor-not-allowed @endif"
                    @if($userHasReachedMaxAppointments) disabled @endif wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-wait">
                                    <span wire:loading wire:target="bookAppointment" class="mr-2">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                            </circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </span>
                                    <span wire:loading.remove wire:target="bookAppointment">
                                        Confirmar Agendamento
                                    </span>
                                    <span wire:loading wire:target="bookAppointment">
                                        Processando...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif
    </div>