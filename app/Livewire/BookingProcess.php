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
use App\Models\BlockedPeriod;

class BookingProcess extends Component
{
    public Collection $services;
    public $selectedServiceId = null;
    public $selectedDate = null;
    public array $availableTimeSlots = []; // Mude para array
    public $selectedTimeSlot = null;
    public $userHasActiveUpcomingAppointment = false; // Nova propriedade para feedback no frontend

    public function mount()
    {
        $this->services = Service::orderBy('name')->get();
        $this->selectedDate = now()->format('Y-m-d');
        $this->checkUserActiveAppointmentStatus(); // Verifica no carregamento
    }

    public function checkUserActiveAppointmentStatus()
    {
        $userId = Auth::id();
        if ($userId) {
            $count = Appointment::where('user_id', $userId)
                ->where('appointment_time', '>=', Carbon::now()) // Agendamentos futuros a partir de agora
                ->whereIn('status', ['pendente', 'confirmado'])
                ->count();
            $this->userHasActiveUpcomingAppointment = $count > 0;
        } else {
            $this->userHasActiveUpcomingAppointment = false; // Se não estiver logado, não tem
        }
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
        $now = Carbon::now(); // Pega a data e hora atuais
        Log::info("Data parseada para cálculo de slots: " . $date->toDateString() . ". Agora é: " . $now->toDateTimeString());


        // Não carregar slots para datas completamente no passado
        if ($date->isPast() && !$date->isToday()) {
            Log::info("SAINDO de loadAvailableTimeSlots: Data selecionada ({$date->toDateString()}) está completamente no passado.");
            return;
        }

        $dayOfWeek = $date->dayOfWeekIso;
        if ($dayOfWeek < Carbon::TUESDAY || $dayOfWeek > Carbon::SATURDAY) {
            Log::info("SAINDO de loadAvailableTimeSlots: Barbearia fechada no dia da semana {$dayOfWeek} para data {$date->toDateString()}.");
            return;
        }

        $openingTime = $date->copy()->hour(8)->minute(0)->second(0);
        $closingTime = $date->copy()->hour(18)->minute(0)->second(0);
        $lunchStartTime = $date->copy()->hour(12)->minute(0)->second(0);
        $lunchEndTime = $date->copy()->hour(13)->minute(0)->second(0);

        // ... (busca de agendamentos existentes e períodos bloqueados continua igual) ...
        $queryDateString = $date->toDateString();
        $existingAppointments = Appointment::whereDate('appointment_time', $queryDateString)
            ->whereIn('status', ['pendente', 'confirmado'])
            ->get();
        $blockedPeriods = BlockedPeriod::where(function ($query) use ($date) {
            $query->where('start_datetime', '<=', $date->copy()->endOfDay())
                ->where('end_datetime', '>=', $date->copy()->startOfDay());
        })
            ->get();


        $timeSlots = [];
        $currentTime = $openingTime->copy();
        $stepMinutes = 15;

        Log::info("Iniciando geração de slots de {$openingTime->format('H:i')} até {$closingTime->format('H:i')}");

        while ($currentTime->copy()->addMinutes($serviceDuration)->lte($closingTime)) {
            $slotStart = $currentTime->copy();
            $slotEnd = $currentTime->copy()->addMinutes($serviceDuration);

            // NOVA VERIFICAÇÃO: Se a data é hoje, o slot de início deve ser no futuro (ou agora)
            if ($date->isToday() && $slotStart->lt($now)) {
                $currentTime->addMinutes($stepMinutes);
                continue; // Pula para o próximo slot potencial pois este já passou
            }

            $slotInLunch = ($slotStart->lt($lunchEndTime) && $slotEnd->gt($lunchStartTime));
            $slotConflictsWithExisting = false;
            // ... (lógica de conflito com existentes) ...
            if (!$slotInLunch) {
                foreach ($existingAppointments as $existingAppointment) {
                    $existingStart = Carbon::parse($existingAppointment->appointment_time);
                    $existingServiceForApp = Service::find($existingAppointment->service_id);
                    if (!$existingServiceForApp) continue;
                    $existingEnd = $existingStart->copy()->addMinutes($existingServiceForApp->duration_minutes);
                    if ($slotStart->lt($existingEnd) && $slotEnd->gt($existingStart)) {
                        $slotConflictsWithExisting = true;
                        break;
                    }
                }
            }

            $slotConflictsWithBlockedPeriod = false;
            // ... (lógica de conflito com bloqueados) ...
            if (!$slotInLunch && !$slotConflictsWithExisting) {
                foreach ($blockedPeriods as $blockedPeriod) {
                    if ($slotStart->lt($blockedPeriod->end_datetime) && $slotEnd->gt($blockedPeriod->start_datetime)) {
                        $slotConflictsWithBlockedPeriod = true;
                        break;
                    }
                }
            }

            if (!$slotInLunch && !$slotConflictsWithExisting && !$slotConflictsWithBlockedPeriod) {
                $timeSlots[] = $slotStart->format('H:i');
            }

            $currentTime->addMinutes($stepMinutes);
        }
        $this->availableTimeSlots = array_unique($timeSlots);
        Log::info("Slots disponíveis calculados para {$date->toDateString()}: " . (!empty($this->availableTimeSlots) ? implode(', ', $this->availableTimeSlots) : 'Nenhum'));
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
        $userId = Auth::id(); // Pega o ID do usuário logado

        // **NOVA VERIFICAÇÃO: Impedir múltiplos agendamentos ativos**
        $activeAppointmentsCount = Appointment::where('user_id', $userId)
            ->where('appointment_time', '>=', Carbon::now()) // Considera a partir do momento atual
            ->whereIn('status', ['pendente', 'confirmado'])
            ->count();

        if ($activeAppointmentsCount > 0) {
            session()->flash('error', 'Você já possui um agendamento futuro ativo. Por favor, aguarde a realização ou cancele o agendamento existente para marcar um novo.');
            $this->checkUserActiveAppointmentStatus(); // Atualiza o status para o frontend
            return;
        }
        // **FIM DA NOVA VERIFICAÇÃO**

        if (!$this->selectedServiceId || !$this->selectedDate || !$this->selectedTimeSlot) {
            session()->flash('error', 'Por favor, selecione serviço, data e horário.');
            $this->checkUserActiveAppointmentStatus();
            return;
        }

        $appointmentDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTimeSlot);
        $now = Carbon::now();

        if ($appointmentDateTime->lt($now->copy()->subMinutes(1))) {
            session()->flash('error', 'Não é possível agendar para um horário que já passou.');
            $this->selectedTimeSlot = null;
            $this->loadAvailableTimeSlots();
            $this->checkUserActiveAppointmentStatus();
            return;
        }

        Appointment::create([
            'user_id' => $userId,
            'service_id' => $this->selectedServiceId,
            'appointment_time' => $appointmentDateTime,
            'status' => 'pendente',
        ]);

        session()->flash('success', 'Seu agendamento para ' . $appointmentDateTime->format('d/m/Y') . ' às ' . $appointmentDateTime->format('H:i') . ' foi solicitado com sucesso! Aguarde a confirmação.');

        $this->selectedTimeSlot = null;
        $this->loadAvailableTimeSlots();
        $this->checkUserActiveAppointmentStatus(); // Re-verifica após o agendamento
    }

    public function render()
    {
        // Se você quiser que o status $userHasActiveUpcomingAppointment seja verificado a cada render,
        // poderia chamar $this->checkUserActiveAppointmentStatus(); aqui também, mas pode ser excessivo.
        // Chamar no mount e após ações como bookAppointment é geralmente suficiente.
        return view('livewire.booking-process');
    }
}
