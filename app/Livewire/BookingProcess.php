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
use Illuminate\Support\Collection as IlluminateCollection; // Não parece estar sendo usada, pode remover
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User; // IMPORTANTE: Importar o modelo User

class BookingProcess extends Component
{
    const MAX_ACTIVE_APPOINTMENTS = 4;
    public IlluminateCollection $services; // Não parece estar sendo usada, pode remover
    public $selectedServiceId = null;
    public ?Service $selectedService = null;
    public $selectedDate = null;
    public array $availableTimeSlots = [];
    public $selectedTimeSlot = null;
    public bool $userHasReachedMaxAppointments = false;

    // Novas propriedades para agendamento pelo admin
    public $targetUserId = null;    // Recebe o ID do cliente para quem o admin está agendando
    public ?User $clientBeingBookedFor = null; // Instância do modelo User do cliente alvo
    public bool $isAdminBookingMode = false;   // Flag para indicar se é um admin agendando

    public string $pageTitle = ''; // Para o título dinâmico da página

     /**
     * Método mount é chamado quando o componente é inicializado.
     * @param int|null $targetUserId - O ID do usuário para quem o agendamento está sendo feito (se aplicável).
     */
    public function mount($targetUserId = null)
    {
        $this->services = Service::orderBy('name')->get();
        if (!$this->selectedDate) {
            $this->selectedDate = now()->format('Y-m-d');
        }

        $loggedInUser = Auth::user();

        if ($loggedInUser && $loggedInUser->is_admin && $targetUserId !== null) {
            // Admin está agendando para um cliente específico
            $this->targetUserId = $targetUserId;
            $this->clientBeingBookedFor = User::find($this->targetUserId);

            if (!$this->clientBeingBookedFor || $this->clientBeingBookedFor->is_admin) {
                session()->flash('error', 'Cliente inválido ou não encontrado para agendamento.');
                $this->isAdminBookingMode = false; // Reseta se o cliente for inválido
                $this->pageTitle = __('Erro ao Carregar Agendamento');
                // Idealmente, impedir o restante do carregamento ou redirecionar
            } else {
                $this->isAdminBookingMode = true;
                $this->pageTitle = __('Agendando para: ') . $this->clientBeingBookedFor->name;
            }
        } elseif ($loggedInUser) {
            // Cliente logado agendando para si mesmo
            $this->targetUserId = $loggedInUser->id;
            $this->clientBeingBookedFor = $loggedInUser;
            $this->isAdminBookingMode = false;
            $this->pageTitle = __('Faça seu Agendamento');
        } else {
            // Usuário não logado (o bookAppointment irá barrar, mas preparamos o título)
            $this->pageTitle = __('Faça seu Agendamento');
        }


        if ($this->selectedServiceId && !$this->selectedService) {
            $this->selectedService = Service::find($this->selectedServiceId);
        }
        // A verificação de limite agora usa o cliente alvo
        $this->checkClientAppointmentLimit();
        $this->loadAvailableTimeSlots();
    }

    public function checkClientAppointmentLimit()
    {
        if ($this->clientBeingBookedFor) { // Verifica se temos um cliente alvo
            $activeAppointmentsCount = Appointment::where('user_id', $this->clientBeingBookedFor->id)
                ->where('appointment_time', '>=', Carbon::now())
                ->whereIn('status', ['pendente', 'confirmado'])
                ->count();
            $this->userHasReachedMaxAppointments = $activeAppointmentsCount >= self::MAX_ACTIVE_APPOINTMENTS;
        } else {
            // Se não há cliente alvo (ex: admin não selecionou, ou usuário não logado tentando agendar)
            // A lógica em bookAppointment irá barrar o agendamento.
            // Definimos como false para não mostrar a mensagem de limite indevidamente.
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

    /**
     * Este método será chamado pelo frontend (AlpineJS) para obter
     * os dias que têm alguma disponibilidade para o serviço selecionado
     * no mês e ano fornecidos.
     *
     * @param int $year
     * @param int $month (1-12)
     * @return array
     */
    public function getCalendarAvailability($year, $month)
    {
        if (!$this->selectedService) {
            return []; // Nenhum serviço selecionado, nenhum dia disponível para destacar
        }

        $serviceId = $this->selectedService->id;
        $serviceDuration = $this->selectedService->duration_minutes;

        // Chave de cache opcional
        $cacheKey = "calendar-availability-{$year}-{$month}-service-{$serviceId}";
        $cacheDuration = now()->addMinutes(10); // Cache por 10 minutos, por exemplo

        // Tenta buscar do cache primeiro
        // Removido o cache para simplificar o exemplo inicial e garantir que os dados são sempre frescos.
        // Se a performance for um problema, o cache pode ser reintroduzido.
        // Log::info("[CalendarAvailability] Buscando para {$year}-{$month}, Serviço ID: {$serviceId}");

        $firstDayOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth()->startOfDay();
        $availableDays = [];

        // Otimização: Buscar todos os agendamentos e períodos bloqueados para o mês inteiro de uma vez
        $appointmentsInMonth = Appointment::whereIn('status', ['pendente', 'confirmado'])
            ->whereBetween('appointment_time', [$firstDayOfMonth, $lastDayOfMonth->copy()->endOfDay()])
            ->with('service:id,duration_minutes') // Eager load para ter a duração correta
            ->get();

        $blockedPeriodsInMonth = BlockedPeriod::where('start_datetime', '<=', $lastDayOfMonth->copy()->endOfDay())
            ->where('end_datetime', '>=', $firstDayOfMonth)
            ->get();

        for ($date = $firstDayOfMonth->copy(); $date->lte($lastDayOfMonth); $date->addDay()) {
            // Pular dias passados (exceto hoje) e dias não úteis da barbearia
            if ($date->isPast() && !$date->isToday()) {
                continue;
            }
            $dayOfWeek = $date->dayOfWeekIso; // Segunda = 1, ..., Domingo = 7
            if ($dayOfWeek < Carbon::TUESDAY || $dayOfWeek > Carbon::SATURDAY) { // Funciona de Terça a Sábado
                continue;
            }

            // Filtrar agendamentos e períodos bloqueados apenas para o dia atual para otimizar os loops internos
            $dateString = $date->toDateString();
            $appointmentsForThisDay = $appointmentsInMonth->filter(function ($appointment) use ($dateString) {
                return Carbon::parse($appointment->appointment_time)->toDateString() === $dateString;
            });
            $blockedPeriodsForThisDay = $blockedPeriodsInMonth->filter(function ($period) use ($date) {
                return Carbon::parse($period->start_datetime)->lte($date->copy()->endOfDay()) &&
                    Carbon::parse($period->end_datetime)->gte($date->copy()->startOfDay());
            });


            // Lógica simplificada de `loadAvailableTimeSlots` para verificar se há *algum* slot
            $openingTime = $date->copy()->hour(8)->minute(0)->second(0);
            $closingTime = $date->copy()->hour(18)->minute(0)->second(0);
            $lunchStartTime = $date->copy()->hour(12)->minute(0)->second(0);
            $lunchEndTime = $date->copy()->hour(13)->minute(0)->second(0);
            $stepMinutes = 15; // Granularidade para checar início de slots
            $now = Carbon::now();
            $currentTime = $openingTime->copy();
            $foundSlotForDay = false;

            while ($currentTime->copy()->addMinutes($serviceDuration)->lte($closingTime)) {
                $slotStart = $currentTime->copy();
                $slotEnd = $slotStart->copy()->addMinutes($serviceDuration);

                // Já passou? (Considerando o início do slot)
                if ($date->isToday() && $slotStart->lt($now)) {
                    $currentTime->addMinutes($stepMinutes);
                    continue;
                }

                // Conflito com almoço?
                if ($slotStart->lt($lunchEndTime) && $slotEnd->gt($lunchStartTime)) {
                    if ($currentTime->lt($lunchEndTime))
                        $currentTime = $lunchEndTime->copy();
                    else
                        $currentTime->addMinutes($stepMinutes);
                    continue;
                }

                // Conflito com agendamentos existentes no dia?
                $conflictWithExisting = false;
                foreach ($appointmentsForThisDay as $existingAppointment) {
                    $existingStart = Carbon::parse($existingAppointment->appointment_time);
                    $existingServiceDur = $existingAppointment->service->duration_minutes ?? 60; // Usa a duração do serviço do agendamento
                    $existingEnd = $existingStart->copy()->addMinutes($existingServiceDur);
                    if ($slotStart->lt($existingEnd) && $slotEnd->gt($existingStart)) {
                        $conflictWithExisting = true;
                        break;
                    }
                }
                if ($conflictWithExisting) {
                    $currentTime->addMinutes($stepMinutes);
                    continue;
                }

                // Conflito com períodos bloqueados no dia?
                $conflictWithBlocked = false;
                foreach ($blockedPeriodsForThisDay as $blockedPeriod) {
                    if ($slotStart->lt(Carbon::parse($blockedPeriod->end_datetime)) && $slotEnd->gt(Carbon::parse($blockedPeriod->start_datetime))) {
                        $conflictWithBlocked = true;
                        break;
                    }
                }
                if ($conflictWithBlocked) {
                    $currentTime->addMinutes($stepMinutes);
                    continue;
                }

                // Se chegou aqui, encontramos um slot disponível para este dia!
                $foundSlotForDay = true;
                break; // Sai do loop while, pois só precisamos saber se HÁ disponibilidade
            }

            if ($foundSlotForDay) {
                $availableDays[] = $date->toDateString(); // Adiciona 'YYYY-MM-DD'
            }
        }

        // Log::info("[CalendarAvailability] Datas disponíveis para {$year}-{$month}, Serviço ID {$serviceId}: ", $availableDays);
        return $availableDays;
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
        $loggedInUser = Auth::user(); // O usuário que está realizando a ação (pode ser o admin)

        if (!$this->clientBeingBookedFor) { // Verifica se temos um cliente para quem agendar
            if ($loggedInUser) {
                session()->flash('error', 'Cliente para o agendamento não especificado.');
            } else {
                session()->flash('error', 'Você precisa estar logado para fazer um agendamento.');
                return redirect()->route('login');
            }
            return;
        }

        // Se for um admin agendando, o limite de agendamentos se aplica ao cliente, não ao admin.
        // A flag $this->userHasReachedMaxAppointments já foi calculada para $this->clientBeingBookedFor.
        if ($this->userHasReachedMaxAppointments) {
            $clientName = $this->clientBeingBookedFor->name;
            session()->flash('error', "O cliente {$clientName} atingiu o limite de " . self::MAX_ACTIVE_APPOINTMENTS . ' agendamento(s) futuro(s) ativo(s).');
            return;
        }

        // Verifica se a conta do CLIENTE está ativa, se for um admin agendando.
        // Se for o próprio cliente agendando, a verificação de conta ativa já foi feita no middleware global ou na tentativa de login.
        // Esta verificação é mais uma garantia no ponto de agendamento se o admin tentar agendar para um cliente inativo.
        // Decisão de negócio: admin PODE ou NÃO PODE agendar para cliente inativo?
        // Se o admin PODE agendar para um cliente inativo, remova este bloco.
        // Se o admin NÃO PODE, mantenha este bloco.
        if ($this->isAdminBookingMode && !$this->clientBeingBookedFor->is_active) {
             session()->flash('error', "A conta do cliente {$this->clientBeingBookedFor->name} está desativada. Ative a conta antes de agendar.");
             return;
        }
        // Se for o próprio cliente agendando (não admin) e a conta dele for desativada após o login,
        // o middleware global CheckAccountIsActive já deve ter deslogado ele.
        // Mas uma verificação aqui não faz mal, como fizemos antes:
        if (!$this->isAdminBookingMode && $loggedInUser instanceof User && !$loggedInUser->is_active) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            Log::info("[BookingProcess] Usuário {$loggedInUser->email} tentou agendar com conta INATIVA e foi deslogado.");
            session()->flash('error', 'Sua conta foi desativada. Por favor, entre em contato com o suporte.');
            return redirect()->route('login');
        }


        if (!$this->selectedService || !$this->selectedDate || !$this->selectedTimeSlot) {
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
        
        DB::beginTransaction();

        try {
            if (!$this->isSlotStillAvailable($appointmentDateTime, $this->selectedService->id, $this->selectedService->duration_minutes)) {
                DB::rollBack();
                session()->flash('error', 'Desculpe, este horário foi agendado por outra pessoa ou tornou-se indisponível enquanto você confirmava. Por favor, escolha outro.');
                $this->selectedTimeSlot = null;
                $this->loadAvailableTimeSlots();
                return;
            }

            $appointment = Appointment::create([
                'user_id' => $this->clientBeingBookedFor->id, // << MUITO IMPORTANTE: ID do cliente para quem se está agendando
                'service_id' => $this->selectedService->id,
                'appointment_time' => $appointmentDateTime,
                'status' => 'pendente',
            ]);

            try {
                // E-mail para o cliente para quem o agendamento foi feito
                Mail::to($this->clientBeingBookedFor->email)->queue(new BookingRequestedToClient($appointment));
                
                $adminEmailForNotification = config('mail.admin_address', env('ADMIN_EMAIL_ADDRESS'));
                if ($adminEmailForNotification) {
                    // O Mailable pode ser ajustado para indicar se um admin fez o agendamento
                    $adminWhoBooked = $this->isAdminBookingMode ? $loggedInUser : null;
                    Mail::to($adminEmailForNotification)->queue(new NewBookingNotificationToAdmin($appointment, $adminWhoBooked));
                }
                Log::info("E-mails de solicitação de agendamento ENFILEIRADOS para o agendamento ID: {$appointment->id} (Cliente: {$this->clientBeingBookedFor->email}).");

            } catch (\Exception $e) {
                Log::error('Erro ao ENFILEIRAR e-mail de novo agendamento ID ' . $appointment->id . ': ' . $e->getMessage());
            }

            DB::commit();

            $clientNameDisplay = $this->isAdminBookingMode ? "para {$this->clientBeingBookedFor->name} " : "";
            session()->flash('success', "Agendamento {$clientNameDisplay}em {$appointmentDateTime->format('d/m/Y')} às {$appointmentDateTime->format('H:i')} foi solicitado com sucesso! Aguarde a confirmação.");

            $this->selectedTimeSlot = null;
            $this->loadAvailableTimeSlots();
            $this->checkClientAppointmentLimit(); // Recalcula o limite para o cliente correto

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro crítico ao criar agendamento: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            session()->flash('error', 'Ocorreu um erro crítico ao tentar realizar seu agendamento. Por favor, tente novamente mais tarde.');
            $this->selectedTimeSlot = null;
            $this->loadAvailableTimeSlots();
        }
    }

    public function render()
    {
        // A variável $pageTitle já está sendo definida no mount
        return view('livewire.booking-process', [
            'displayNameForBooking' => $this->pageTitle
        ]);
    }
    protected function isSlotStillAvailable(Carbon $slotStart, int $serviceId, int $serviceDurationMinutes): bool
    {
        $slotEnd = $slotStart->copy()->addMinutes($serviceDurationMinutes);
        $dateForQuery = $slotStart->toDateString(); // Para otimizar as consultas para o dia específico

        // 1. Conflito com horário de almoço (Re-verificação de segurança)
        // Assumindo que loadAvailableTimeSlots já filtrou isso, mas uma checagem rápida aqui é barata.
        $lunchStartTime = $slotStart->copy()->hour(12)->minute(0)->second(0);
        $lunchEndTime = $slotStart->copy()->hour(13)->minute(0)->second(0);
        if ($slotStart->lt($lunchEndTime) && $slotEnd->gt($lunchStartTime)) {
            Log::warning("[RaceConditionCheck] Tentativa de agendamento em horário de almoço para {$slotStart->toDateTimeString()}");
            return false;
        }

        // 2. Conflito com agendamentos existentes (pendentes ou confirmados)
        // Buscamos apenas agendamentos que PODEM conflitar no mesmo dia
        $conflictingAppointments = Appointment::whereIn('status', ['pendente', 'confirmado'])
            ->whereDate('appointment_time', $dateForQuery) // Filtra pelo dia
            ->where('appointment_time', '<', $slotEnd)     // O agendamento existente deve começar antes que o novo termine
            // E o agendamento existente deve terminar depois que o novo começa (verificado no loop)
            ->with('service:id,duration_minutes')          // Eager load para pegar a duração correta
            ->get();

        foreach ($conflictingAppointments as $existingAppointment) {
            $existingStart = Carbon::parse($existingAppointment->appointment_time);
            $existingDuration = $existingAppointment->service ? $existingAppointment->service->duration_minutes : 60; // Duração padrão
            $existingEnd = $existingStart->copy()->addMinutes($existingDuration);

            // Verifica sobreposição: (InícioA < FimN) E (FimA > InícioN)
            if ($slotStart->lt($existingEnd) && $slotEnd->gt($existingStart)) {
                Log::warning("[RaceConditionCheck] Conflito de agendamento detectado. Novo: {$slotStart->toDateTimeString()}-{$slotEnd->toDateTimeString()}. Existente: {$existingStart->toDateTimeString()}-{$existingEnd->toDateTimeString()} (ID: {$existingAppointment->id})");
                return false; // Conflito encontrado
            }
        }

        // 3. Conflito com períodos bloqueados
        $blockedConflict = BlockedPeriod::where('start_datetime', '<', $slotEnd) // InícioBloqueio < FimN
            ->where('end_datetime', '>', $slotStart)   // FimBloqueio > InícioN
            // Adicionar filtro de data para otimizar
            ->where(function ($query) use ($dateForQuery) {
                $query->whereDate('start_datetime', '<=', $dateForQuery)
                    ->whereDate('end_datetime', '>=', $dateForQuery);
            })
            ->exists();

        if ($blockedConflict) {
            Log::warning("[RaceConditionCheck] Conflito com período bloqueado detectado para {$slotStart->toDateTimeString()}");
            return false; // Conflito encontrado
        }

        Log::info("[RaceConditionCheck] Slot {$slotStart->toDateTimeString()} ainda disponível na verificação final.");
        return true; // Slot disponível
    }
}