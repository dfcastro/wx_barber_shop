{{-- resources/views/livewire/booking-process.blade.php --}}
<div>
    {{-- ETAPA 1: Selecionar Serviço --}}
    <div class="mb-6">
        <label for="service" class="block text-sm font-medium text-gray-700">1. Escolha o Serviço:</label>
        <select wire:model.lazy="selectedServiceId" id="service" name="service"
            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <option value="">Selecione um serviço...</option>
            @foreach ($services as $service)
            <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration_minutes }} min - R$ {{ number_format($service->price, 2, ',', '.') }})</option>
            @endforeach
        </select>
        @error('selectedServiceId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- ETAPA 2: Selecionar Data (e mostrar calendário no futuro) --}}
    @if ($selectedServiceId)
    <div class="mb-6">
        <label for="date" class="block text-sm font-medium text-gray-700">2. Escolha a Data:</label>
        <input type="date" wire:model.lazy="selectedDate" id="date" name="date"
            min="{{ now()->format('Y-m-d') }}" {{-- Não permite datas passadas --}}
            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        @error('selectedDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    @endif

    {{-- ETAPA 3: Selecionar Horário --}}
    @if ($selectedServiceId && $selectedDate && !empty($availableTimeSlots))
    <div class="mb-6">
        <h3 class="text-sm font-medium text-gray-700">3. Escolha um Horário para {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}:</h3>
        <div class="mt-2 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
            @foreach ($availableTimeSlots as $slot)
            <button type="button" wire:click="selectTimeSlot('{{ $slot }}')"
                class="py-2 px-3 border rounded-md text-sm font-medium
                                   {{ $selectedTimeSlot == $slot ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-100 hover:bg-gray-200 border-gray-300' }}">
                {{ $slot }}
            </button>
            @endforeach
        </div>
        @if (empty($availableTimeSlots) && $selectedServiceId && $selectedDate)
        <p class="text-sm text-gray-500 mt-2">Nenhum horário disponível para esta data e serviço.</p>
        @endif
    </div>
    @elseif ($selectedServiceId && $selectedDate)
    <div class="mb-6">
        <p class="text-sm text-gray-500 mt-2">Carregando horários ou nenhum horário disponível...</p>
        {{-- Poderíamos adicionar um spinner de carregamento aqui com wire:loading --}}
    </div>
    @endif

    {{-- ETAPA 4: Confirmar Agendamento --}}
    @if ($selectedServiceId && $selectedDate && $selectedTimeSlot)
    <div class="mt-8">
        <button wire:click="bookAppointment" type="button"
            class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Confirmar Agendamento para {{ $selectedTimeSlot }} em {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}
        </button>
    </div>
    @endif

    {{-- Para depuração inicial, podemos mostrar as variáveis --}}

    {{--<div class="mt-6 p-4 bg-gray-100 rounded">
        <h4 class="font-bold">Debug:</h4>
        <p>Serviço ID: {{ $selectedServiceId ?? 'Nenhum' }}</p>
    <p>Data: {{ $selectedDate ?? 'Nenhuma' }}</p>
    <p>Horários: {{ implode(', ', $availableTimeSlots) }}</p>
    <p>Horário Selecionado: {{ $selectedTimeSlot ?? 'Nenhum' }}</p>
</div>--}}

</div>