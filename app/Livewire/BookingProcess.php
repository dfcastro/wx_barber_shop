<?php

namespace App\Livewire; // Ou App\Http\Livewire se sua estrutura for mais antiga

use App\Models\Service;
use App\Models\Appointment;
use App\Models\BlockedPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingRequestedToClient;
use App\Mail\NewBookingNotificationToAdmin;
use Livewire\Component;
use Illuminate\Support\Collection as IlluminateCollection; // Renomeado para evitar conflito se houver outra 'Collection'
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingProcess extends Component
{
    // Defina o limite máximo de agendamentos futuros ativos por cliente
    const MAX_ACTIVE_APPOINTMENTS = 4; // << Altere este valor conforme necessário

    public IlluminateCollection $services; // Coleção de todos os serviços disponíveis
    public $selectedServiceId = null;     // ID do serviço selecionado pelo usuário
    public $selectedDate = null;          // Data selecionada pelo usuário (ex: '2025-12-31')
    public array $availableTimeSlots = []; // Array de horários disponíveis
    public $selectedTimeSlot = null;      // Horário específico selecionado

    public bool $userHasReachedMaxAppointments = false; // Para feedback no frontend

    public function mount()
    {
        $this->services = Service::orderBy('name')->get();
        if (!$this->selectedDate) { // Define a data apenas se não estiver já definida (útil para re-renderizações)
            $this->selectedDate = now()->format('Y-m-d');
        }
        $this->checkAppointmentLimit(); // Verifica o limite ao carregar
        $this->loadAvailableTimeSlots(); // Carrega os slots para a data inicial e serviço (se houver)
    }

    /**
     * Verifica se o usuário atingiu o limite de agendamentos e atualiza a propriedade.
     */
    public function checkAppointmentLimit()
    {
        if (Auth::check()) {
            $activeAppointmentsCount = Appointment::where('user_id', Auth::id())
                ->where('appointment_time', '>=', Carbon::now()) // Agendamentos futuros a partir de agora
                ->whereIn('status', ['pendente', 'confirmado'])
                ->count();
            $this->userHasReachedMaxAppointments = $activeAppointmentsCount >= self::MAX_ACTIVE_APPOINTMENTS;
        } else {
            $this->userHasReachedMaxAppointments = false;
        }
    }

    public function updatedSelectedServiceId($serviceId)
    {
        $this->selectedTimeSlot = null; // Reseta horário ao mudar serviço
        $this->availableTimeSlots = []; // Limpa slots para forçar recálculo ou mostrar carregando
        if ($serviceId) {
            $this->loadAvailableTimeSlots();
        }
    }

    public function updatedSelectedDate($date)
    {
        Log::info(">>> updatedSelectedDate HOOK EXECUTADO. Nova data selecionada: " . $date . ". Valor ATUAL de \$this->selectedDate: " . $this->selectedDate);
        $this->selectedTimeSlot = null; // Reseta horário ao mudar data
        $this->availableTimeSlots = []; // Limpa slots para forçar recálculo ou mostrar carregando
        if ($date) {
            $this->loadAvailableTimeSlots();
        }
        Log::info(">>> updatedSelectedDate HOOK FINALIZADO. \$this->selectedDate AGORA é: " . $this->selectedDate);
    }

    public function loadAvailableTimeSlots()
    {
        $this->availableTimeSlots = []; // Sempre limpa antes de recalcular
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
        $now = Carbon::now();
        Log::info("Data parseada para cálculo de slots: " . $date->toDateString() . ". Agora é: " . $now->toDateTimeString());

        if ($date->isPast() && !$date->isToday()) {
            Log::info("SAINDO de loadAvailableTimeSlots: Data selecionada ({$date->toDateString()}) está completamente no passado.");
            return;
        }

        $dayOfWeek = $date->dayOfWeekIso; // Segunda = 1, ..., Domingo = 7
        if ($dayOfWeek < Carbon::TUESDAY || $dayOfWeek > Carbon::SATURDAY) { // Terça (2) a Sábado (6)
            Log::info("SAINDO de loadAvailableTimeSlots: Barbearia fechada no dia da semana {$dayOfWeek} para data {$date->toDateString()}.");
            return;
        }

        $openingTime = $date->copy()->hour(8)->minute(0)->second(0);
        $closingTime = $date->copy()->hour(18)->minute(0)->second(0);
        $lunchStartTime = $date->copy()->hour(12)->minute(0)->second(0);
        $lunchEndTime = $date->copy()->hour(13)->minute(0)->second(0);

        $queryDateString = $date->toDateString();
        $existingAppointments = Appointment::whereDate('appointment_time', $queryDateString)
                                          ->whereIn('status', ['pendente', 'confirmado'])
                                          ->get();
        Log::info("Encontrados " . $existingAppointments->count() . " agendamentos existentes para " . $queryDateString);

        $blockedPeriods = BlockedPeriod::where(function ($query) use ($date) {
                                            $query->where('start_datetime', '<=', $date->copy()->endOfDay())
                                                  ->where('end_datetime', '>=', $date->copy()->startOfDay());
                                        })
                                        ->get();
        Log::info("Encontrados " . $blockedPeriods->count() . " períodos bloqueados relevantes para " . $queryDateString);

        $timeSlots = [];
        $currentTime = $openingTime->copy();
        $stepMinutes = 15;

        Log::info("Iniciando geração de slots de {$openingTime->format('H:i')} até {$closingTime->format('H:i')} com passo de {$stepMinutes}min e duração de serviço de {$serviceDuration}min.");

        while ($currentTime->copy()->addMinutes($serviceDuration)->lte($closingTime)) {
            $slotStart = $currentTime->copy();
            $slotEnd = $currentTime->copy()->addMinutes($serviceDuration);

            if ($date->isToday() && $slotStart->lt($now)) {
                $currentTime->addMinutes($stepMinutes);
                continue;
            }

            $slotInLunch = ($slotStart->lt($lunchEndTime) && $slotEnd->gt($lunchStartTime));
            $slotConflictsWithExisting = false;
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

    public function selectTimeSlot($timeSlot)
    {
        $this->selectedTimeSlot = $timeSlot;
    }

    public function bookAppointment()
    {
        $userId = Auth::id();
        if (!$userId) {
            session()->flash('error', 'Você precisa estar logado para fazer um agendamento.');
            return;
        }

        // Re-verifica o limite ANTES de qualquer outra coisa
        $this->checkAppointmentLimit(); // Garante que a propriedade está atualizada
        if ($this->userHasReachedMaxAppointments) {
            session()->flash('error', 'Você atingiu o limite de ' . self::MAX_ACTIVE_APPOINTMENTS . ' agendamento(s) futuro(s) ativo(s). Por favor, aguarde a realização ou cancele um agendamento existente para marcar um novo.');
            return;
        }

        if (!$this->selectedServiceId || !$this->selectedDate || !$this->selectedTimeSlot) {
            session()->flash('error', 'Por favor, selecione serviço, data e horário.');
            return;
        }

        $appointmentDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTimeSlot);
        $now = Carbon::now();

        if ($appointmentDateTime->lt($now->copy()->subMinutes(1))) {
            session()->flash('error', 'Não é possível agendar para um horário que já passou.');
            $this->selectedTimeSlot = null;
            $this->loadAvailableTimeSlots();
            return;
        }
        
        // TODO: Adicionar verificação de condição de corrida (se o slot foi ocupado por outro usuário entre o load e o book)
        // Isso pode envolver uma verificação final na base de dados antes do create.

        $appointment = Appointment::create([
            'user_id' => $userId,
            'service_id' => $this->selectedServiceId,
            'appointment_time' => $appointmentDateTime,
            'status' => 'pendente',
        ]);

        try {
            Mail::to($appointment->user->email)->send(new BookingRequestedToClient($appointment));
            $adminEmail = env('ADMIN_EMAIL_ADDRESS'); // ou config('mail.admin_address')
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewBookingNotificationToAdmin($appointment));
            } else {
                Log::warning('ADMIN_EMAIL_ADDRESS não configurado. Não foi possível notificar o admin sobre o novo agendamento ID: ' . $appointment->id);
            }
            Log::info("E-mails de solicitação de agendamento enviados para o agendamento ID: {$appointment->id}.");
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de novo agendamento ID ' . $appointment->id . ': ' . $e->getMessage());
        }

        session()->flash('success', 'Seu agendamento para ' . $appointmentDateTime->format('d/m/Y') . ' às ' . $appointmentDateTime->format('H:i') . ' foi solicitado com sucesso! Aguarde a confirmação por e-mail.');

        $this->selectedTimeSlot = null; // Limpa o horário selecionado
        $this->loadAvailableTimeSlots(); // Recarrega os slots para a data/serviço atual
        $this->checkAppointmentLimit(); // Re-verifica o limite para atualizar o estado do frontend
    }

    public function render()
    {
        // Se for necessário garantir que o limite de agendamentos seja checado
        // a cada renderização (ex: se o usuário abrir duas abas e cancelar em uma),
        // pode-se chamar aqui. Mas pode ser um pouco excessivo.
        // $this->checkAppointmentLimit(); 
        return view('livewire.booking-process');
    }
}