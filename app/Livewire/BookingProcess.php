<?php

namespace App\Livewire;

use App\Models\Service;
use App\Models\Appointment; // Adicione para verificar agendamentos existentes
use Livewire\Component;
use Illuminate\Support\Collection;
use Carbon\Carbon; // Para manipulação de datas e horas
use Carbon\CarbonPeriod; // Para gerar períodos de tempo
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingProcess extends Component
{
    public Collection $services;
    public $selectedServiceId = null;
    public $selectedDate = null;
    public array $availableTimeSlots = []; // Mude para array
    public $selectedTimeSlot = null;

    public function mount()
    {
        $this->services = Service::orderBy('name')->get();
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function updatedSelectedServiceId($serviceId)
    {
        $this->selectedTimeSlot = null; // Reseta horário ao mudar serviço
        $this->loadAvailableTimeSlots();
    }

    public function updatedSelectedDate($date)
    {
        Log::info(">>> updatedSelectedDate HOOK EXECUTADO. Nova data selecionada: " . $date . ". Valor ATUAL de \$this->selectedDate ANTES do hook: " . $this->selectedDate);
        // O Livewire já atualizou $this->selectedDate para o novo valor ANTES de chamar este hook.
        // Então, $this->selectedDate dentro deste método já DEVE ser a nova data.

        $this->selectedTimeSlot = null;
        $this->loadAvailableTimeSlots();
        Log::info(">>> updatedSelectedDate HOOK FINALIZADO. \$this->selectedDate AGORA é: " . $this->selectedDate);
    }

    // app/Livewire/BookingProcess.php
    // ... (outras propriedades e métodos mount, updated*, etc.) ...

    public function loadAvailableTimeSlots()
    {
        $this->availableTimeSlots = [];
        Log::info("----------------------------------------------------");
        Log::info("loadAvailableTimeSlots INICIADO para data: {$this->selectedDate}, serviço ID: {$this->selectedServiceId}");

        if (!$this->selectedServiceId || !$this->selectedDate) {
            Log::info("SAINDO de loadAvailableTimeSlots: Serviço ID ou Data não definidos.");
            return;
        }

        $selectedService = Service::find($this->selectedServiceId);
        if (!$selectedService) {
            Log::info("SAINDO de loadAvailableTimeSlots: Serviço com ID {$this->selectedServiceId} não encontrado.");
            return;
        }

        $serviceDuration = $selectedService->duration_minutes;
        $date = Carbon::parse($this->selectedDate);
        Log::info("Data parseada para cálculo de slots: " . $date->toDateString());

        $dayOfWeek = $date->dayOfWeekIso;
        if ($dayOfWeek < Carbon::TUESDAY || $dayOfWeek > Carbon::SATURDAY) {
            Log::info("SAINDO de loadAvailableTimeSlots: Barbearia fechada no dia da semana {$dayOfWeek} para data {$date->toDateString()}.");
            return;
        }

        $openingTime = $date->copy()->hour(8)->minute(0)->second(0);
        $closingTime = $date->copy()->hour(18)->minute(0)->second(0);
        $lunchStartTime = $date->copy()->hour(12)->minute(0)->second(0);
        $lunchEndTime = $date->copy()->hour(13)->minute(0)->second(0);

        $queryDateString = $date->toDateString();
        Log::info("Consultando agendamentos existentes para a data: " . $queryDateString);
        $existingAppointments = Appointment::whereDate('appointment_time', $queryDateString)
            ->whereIn('status', ['pendente', 'confirmado'])
            ->get();
        Log::info("Encontrados " . $existingAppointments->count() . " agendamentos existentes para " . $queryDateString . ":");
        foreach ($existingAppointments as $app) {
            Log::info(" - Agendamento existente: ID {$app->id} às " . Carbon::parse($app->appointment_time)->format('H:i'));
        }

        $timeSlots = [];
        $currentTime = $openingTime->copy();
        $stepMinutes = 15;

        Log::info("Iniciando geração de slots de {$openingTime->format('H:i')} até {$closingTime->format('H:i')} com passo de {$stepMinutes}min e duração de serviço de {$serviceDuration}min.");

        while ($currentTime->copy()->addMinutes($serviceDuration)->lte($closingTime)) {
            $slotStart = $currentTime->copy();
            $slotEnd = $currentTime->copy()->addMinutes($serviceDuration);

            // Log::info("Verificando slot potencial: {$slotStart->format('H:i')} - {$slotEnd->format('H:i')}");

            $slotInLunch = ($slotStart->lt($lunchEndTime) && $slotEnd->gt($lunchStartTime));
            // if ($slotInLunch) {
            //     Log::info(" -> Slot EM CONFLITO com almoço.");
            // }

            $slotConflictsWithExisting = false;
            if (!$slotInLunch) { // Só checa conflito com existentes se não estiver no almoço
                foreach ($existingAppointments as $existingAppointment) {
                    $existingStart = Carbon::parse($existingAppointment->appointment_time);
                    $existingServiceForApp = Service::find($existingAppointment->service_id);
                    if (!$existingServiceForApp) continue;
                    $existingEnd = $existingStart->copy()->addMinutes($existingServiceForApp->duration_minutes);

                    if ($slotStart->lt($existingEnd) && $slotEnd->gt($existingStart)) {
                        // Log::info(" -> Slot EM CONFLITO com agendamento existente ID {$existingAppointment->id} ({$existingStart->format('H:i')}-{$existingEnd->format('H:i')}).");
                        $slotConflictsWithExisting = true;
                        break;
                    }
                }
            }

            if (!$slotInLunch && !$slotConflictsWithExisting) {
                // Log::info(" -> Slot ADICIONADO: {$slotStart->format('H:i')}");
                $timeSlots[] = $slotStart->format('H:i');
            }

            $currentTime->addMinutes($stepMinutes);
        }
        $this->availableTimeSlots = array_unique($timeSlots);
        Log::info("Slots disponíveis calculados para {$date->toDateString()}: " . implode(', ', $this->availableTimeSlots));
        Log::info("loadAvailableTimeSlots FINALIZADO para data: {$this->selectedDate}");
        Log::info("----------------------------------------------------");
    }
    // ... (render e outros métodos) ...

    public function selectTimeSlot($timeSlot)
    {
        $this->selectedTimeSlot = $timeSlot;
    }
    public function bookAppointment()
    {
        if (!$this->selectedServiceId || !$this->selectedDate || !$this->selectedTimeSlot) {
            session()->flash('error', 'Por favor, selecione serviço, data e horário.');
            return;
        }

        // TODO: Adicionar uma verificação mais robusta aqui para garantir que o slot ainda está disponível
        // antes de criar o agendamento (evitar condição de corrida).

        Appointment::create([
            'user_id' => Auth::id(), // Usando Auth::id() como sugerido para o linter
            'service_id' => $this->selectedServiceId,
            'appointment_time' => Carbon::parse($this->selectedDate . ' ' . $this->selectedTimeSlot),
            'status' => 'pendente',
        ]);

        session()->flash('success', 'Seu agendamento para ' . Carbon::parse($this->selectedDate)->format('d/m/Y') . ' às ' . $this->selectedTimeSlot . ' foi solicitado com sucesso! Aguarde a confirmação.');

        // Limpa apenas o horário selecionado
        $this->selectedTimeSlot = null;

        // Recarrega os horários disponíveis para a data e serviço atuais
        $this->loadAvailableTimeSlots();
    }

    public function render()
    {
        // A chamada para loadAvailableTimeSlots foi movida para os métodos updated*
        // para evitar chamadas excessivas no render.
        // Se for necessário recalcular sempre, pode ser colocada aqui com cuidado.
        return view('livewire.booking-process');
    }
}
