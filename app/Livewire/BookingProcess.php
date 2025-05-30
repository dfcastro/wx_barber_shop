<?php

namespace App\Livewire;

use App\Models\Service;
use App\Models\Appointment;
use App\Models\BlockedPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingRequestedToClient;
use App\Mail\NewBookingNotificationToAdmin;
use Livewire\Component;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingProcess extends Component
{
    const MAX_ACTIVE_APPOINTMENTS = 4;

    public IlluminateCollection $services;
    public $selectedServiceId = null;
    public ?Service $selectedService = null; // << NOVA PROPRIEDADE: Armazena a instância do serviço
    public $selectedDate = null;
    public array $availableTimeSlots = [];
    public $selectedTimeSlot = null;
    public bool $userHasReachedMaxAppointments = false;

    public function mount()
    {
        $this->services = Service::orderBy('name')->get();
        if (!$this->selectedDate) { // Se não vier de um estado anterior
            $this->selectedDate = now()->format('Y-m-d');
        }

        // Se selectedServiceId já estiver definido (ex: estado anterior), carregar o selectedService
        if ($this->selectedServiceId && !$this->selectedService) {
            $this->selectedService = Service::find($this->selectedServiceId);
        }

        $this->checkAppointmentLimit();
        $this->loadAvailableTimeSlots(); // Carrega slots para data/serviço inicial (se houver)
    }

    public function checkAppointmentLimit()
    {
        if (Auth::check()) {
            $activeAppointmentsCount = Appointment::where('user_id', Auth::id())
                ->where('appointment_time', '>=', Carbon::now())
                ->whereIn('status', ['pendente', 'confirmado'])
                ->count();
            $this->userHasReachedMaxAppointments = $activeAppointmentsCount >= self::MAX_ACTIVE_APPOINTMENTS;
        } else {
            $this->userHasReachedMaxAppointments = false;
        }
    }

    // ATUALIZADO: updatedSelectedServiceId
    public function updatedSelectedServiceId($serviceId)
    {
        $this->selectedTimeSlot = null;
        $this->availableTimeSlots = []; // Limpa os slots antigos

        if ($serviceId) {
            $this->selectedService = Service::find($serviceId); // Carrega a instância do serviço aqui
            if (!$this->selectedService) {
                Log::warning("Serviço com ID {$serviceId} não encontrado ao tentar atualizar selectedServiceId.");
                // Opcional: exibir uma mensagem de erro para o usuário ou resetar o selectedServiceId
                $this->selectedServiceId = null; // Reseta o ID se o serviço não for encontrado
            }
        } else {
            $this->selectedService = null;
        }

        // Recarrega os horários disponíveis com o novo serviço (ou nenhum, se $serviceId for nulo)
        $this->loadAvailableTimeSlots();
    }

    public function updatedSelectedDate($date)
    {
        Log::info(">>> updatedSelectedDate HOOK EXECUTADO. Nova data selecionada: " . $date);
        $this->selectedTimeSlot = null;
        $this->availableTimeSlots = [];
        if ($date) {
            $this->loadAvailableTimeSlots();
        }
        Log::info(">>> updatedSelectedDate HOOK FINALIZADO. \$this->selectedDate AGORA é: " . $this->selectedDate);
    }

    // ATUALIZADO: loadAvailableTimeSlots
    public function loadAvailableTimeSlots()
    {
        $this->availableTimeSlots = [];
        Log::info("----------------------------------------------------");
        Log::info("loadAvailableTimeSlots INICIADO para data: {$this->selectedDate}, Serviço: " . ($this->selectedService ? $this->selectedService->name . " (ID: " . $this->selectedService->id . ")" : 'Nenhum'));

        // Utiliza a propriedade $this->selectedService que já foi carregada
        if (!$this->selectedService || !$this->selectedDate) {
            Log::info("SAINDO de loadAvailableTimeSlots: Serviço ou Data não definidos.");
            // Se selectedServiceId está definido mas selectedService não (pode acontecer em re-renderizações complexas)
            // tenta carregar novamente.
            if ($this->selectedServiceId && !$this->selectedService) {
                Log::warning("selectedService é nulo, mas selectedServiceId (" . $this->selectedServiceId . ") está definido. Tentando recarregar o serviço.");
                $this->selectedService = Service::find($this->selectedServiceId);
                if (!$this->selectedService) {
                     Log::error("Falha ao recarregar o serviço com ID " . $this->selectedServiceId . ". Abortando cálculo de slots.");
                     return; // Ainda não pode prosseguir se o serviço não for encontrado
                }
                 Log::info("Serviço recarregado: " . $this->selectedService->name);
            } else if (!$this->selectedService) {
                return; // Se $this->selectedServiceId também for nulo, ou se o serviço não foi encontrado.
            }
        }

        $serviceDuration = $this->selectedService->duration_minutes;
        $date = Carbon::parse($this->selectedDate);
        $now = Carbon::now();
        Log::info("Data parseada para cálculo de slots: " . $date->toDateString() . ". Agora é: " . $now->toDateTimeString());

        if ($date->isPast() && !$date->isToday()) {
            Log::info("SAINDO de loadAvailableTimeSlots: Data selecionada ({$date->toDateString()}) está completamente no passado.");
            return;
        }

        $dayOfWeek = $date->dayOfWeekIso; // Segunda = 1, ..., Domingo = 7
        // Considerando que a barbearia funciona de Terça (2) a Sábado (6)
        if ($dayOfWeek < Carbon::TUESDAY || $dayOfWeek > Carbon::SATURDAY) {
            Log::info("SAINDO de loadAvailableTimeSlots: Barbearia fechada no dia da semana {$dayOfWeek} para data {$date->toDateString()}.");
            return;
        }

        // Horários de funcionamento e almoço (poderiam vir de configurações no futuro)
        $openingTime = $date->copy()->hour(8)->minute(0)->second(0);
        $closingTime = $date->copy()->hour(18)->minute(0)->second(0);
        $lunchStartTime = $date->copy()->hour(12)->minute(0)->second(0);
        $lunchEndTime = $date->copy()->hour(13)->minute(0)->second(0);

        $queryDateString = $date->toDateString();

        // Otimização: Buscar agendamentos e seus serviços (apenas a duração) uma vez para o dia.
        $existingAppointments = Appointment::whereDate('appointment_time', $queryDateString)
                                          ->whereIn('status', ['pendente', 'confirmado'])
                                          ->with('service:id,duration_minutes') // Eager load service duration
                                          ->get();
        Log::info("Encontrados " . $existingAppointments->count() . " agendamentos existentes para " . $queryDateString);

        $blockedPeriodsForDay = BlockedPeriod::where(function ($query) use ($date) {
                                            $query->where('start_datetime', '<=', $date->copy()->endOfDay())
                                                  ->where('end_datetime', '>=', $date->copy()->startOfDay());
                                        })
                                        ->get();
        Log::info("Encontrados " . $blockedPeriodsForDay->count() . " períodos bloqueados relevantes para " . $queryDateString);

        $timeSlots = [];
        $currentTime = $openingTime->copy();
        // Intervalo entre os possíveis inícios de slots. Ex: 08:00, 08:15, 08:30...
        // Ajuste conforme a granularidade desejada para a escolha do cliente.
        $stepMinutes = 15;

        Log::info("Iniciando geração de slots de {$openingTime->format('H:i')} até {$closingTime->format('H:i')} com passo de {$stepMinutes}min e duração do serviço selecionado de {$serviceDuration}min.");

        while ($currentTime->copy()->addMinutes($serviceDuration)->lte($closingTime)) {
            $slotStart = $currentTime->copy();
            $slotEnd = $currentTime->copy()->addMinutes($serviceDuration);

            // 1. Se a data for hoje, não mostrar horários que já passaram (com uma pequena margem)
            if ($date->isToday() && $slotStart->lt($now->copy()->subMinutes(1))) { // Subtrai 1 min para garantir que o slot atual/próximo possa ser pego
                $currentTime->addMinutes($stepMinutes);
                continue;
            }

            // 2. Verificar conflito com horário de almoço
            $slotInLunch = ($slotStart->lt($lunchEndTime) && $slotEnd->gt($lunchStartTime));
            if ($slotInLunch) {
                // Se o início do slot cai no almoço, avançar para o fim do almoço
                // Se o slot começa antes mas termina durante ou depois do almoço, também é conflito.
                // A lógica atual já impede que o slot seja adicionado, mas podemos otimizar o avanço:
                if ($currentTime->lt($lunchEndTime)) {
                     $currentTime = $lunchEndTime->copy(); // Pula $currentTime para o fim do almoço
                } else {
                    $currentTime->addMinutes($stepMinutes);
                }
                continue;
            }

            // 3. Verificar conflito com agendamentos existentes
            $slotConflictsWithExisting = false;
            foreach ($existingAppointments as $existingAppointment) {
                $existingStart = Carbon::parse($existingAppointment->appointment_time);
                $existingServiceDuration = $existingAppointment->service ? $existingAppointment->service->duration_minutes : 60; // Duração padrão caso o serviço não seja encontrado (improvável com eager loading)
                $existingEnd = $existingStart->copy()->addMinutes($existingServiceDuration);

                if ($slotStart->lt($existingEnd) && $slotEnd->gt($existingStart)) {
                    $slotConflictsWithExisting = true;
                    break;
                }
            }
            if ($slotConflictsWithExisting) {
                $currentTime->addMinutes($stepMinutes);
                continue;
            }

            // 4. Verificar conflito com períodos bloqueados
            $slotConflictsWithBlockedPeriod = false;
            foreach ($blockedPeriodsForDay as $blockedPeriod) {
                if ($slotStart->lt($blockedPeriod->end_datetime) && $slotEnd->gt($blockedPeriod->start_datetime)) {
                    $slotConflictsWithBlockedPeriod = true;
                    break;
                }
            }
            if ($slotConflictsWithBlockedPeriod) {
                $currentTime->addMinutes($stepMinutes);
                continue;
            }

            // Se passou por todas as verificações, o slot está disponível
            $timeSlots[] = $slotStart->format('H:i');
            $currentTime->addMinutes($stepMinutes);
        }

        $this->availableTimeSlots = $timeSlots; // array_unique removido, pois a lógica de incremento deve prevenir duplicatas
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

        $this->checkAppointmentLimit();
        if ($this->userHasReachedMaxAppointments) {
            session()->flash('error', 'Você atingiu o limite de ' . self::MAX_ACTIVE_APPOINTMENTS . ' agendamento(s) futuro(s) ativo(s). Por favor, aguarde a realização ou cancele um agendamento existente para marcar um novo.');
            return;
        }

        // Validação dos dados selecionados (já deve estar usando $this->selectedService)
        if (!$this->selectedService || !$this->selectedDate || !$this->selectedTimeSlot) {
            session()->flash('error', 'Por favor, selecione serviço, data e horário.');
            return;
        }

        $appointmentDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTimeSlot);
        $now = Carbon::now();

        if ($appointmentDateTime->lt($now->copy()->subMinutes(1))) {
            session()->flash('error', 'Não é possível agendar para um horário que já passou.');
            $this->selectedTimeSlot = null; // Limpa o slot para forçar nova seleção
            $this->loadAvailableTimeSlots(); // Recarrega os slots
            return;
        }
        
        // TODO: Prevenção de Condição de Corrida (discutido na próxima etapa se você quiser)

        $appointment = Appointment::create([
            'user_id' => $userId,
            'service_id' => $this->selectedService->id, // Usa o ID do serviço da propriedade
            'appointment_time' => $appointmentDateTime,
            'status' => 'pendente',
        ]);

        try {
            Mail::to($appointment->user->email)->send(new BookingRequestedToClient($appointment));
            $adminEmail = config('mail.admin_address', env('ADMIN_EMAIL_ADDRESS', 'dfcastro@outlook.com.br'));
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewBookingNotificationToAdmin($appointment));
            } else {
                Log::warning('ADMIN_EMAIL_ADDRESS não configurado. Não foi possível notificar o admin sobre o novo agendamento ID: ' . $appointment->id);
            }
            Log::info("E-mails de solicitação de agendamento enviados para o agendamento ID: {$appointment->id}.");
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de novo agendamento ID ' . $appointment->id . ': ' . $e->getMessage());
            // Considerar se deve ou não falhar o agendamento inteiro por causa do e-mail.
            // Por enquanto, o agendamento é criado e o erro de e-mail é logado.
        }

        session()->flash('success', 'Seu agendamento para ' . $appointmentDateTime->format('d/m/Y') . ' às ' . $appointmentDateTime->format('H:i') . ' foi solicitado com sucesso! Aguarde a confirmação por e-mail.');

        $this->selectedTimeSlot = null;
        $this->loadAvailableTimeSlots();
        $this->checkAppointmentLimit();
    }

    public function render()
    {
        return view('livewire.booking-process');
    }
}