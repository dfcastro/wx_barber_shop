<?php

namespace App\Livewire\Client;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Carbon\Carbon;

class MyAppointmentsList extends Component
{
    public $upcomingAppointments;
    public $pastAppointments;

    public ?Appointment $appointmentToCancel = null;
    public $cancellationNotAllowedReason = '';

    const MIN_HOURS_BEFORE_CANCELLATION = 4;

    public function mount()
    {
        $this->loadAppointments();
    }

    public function loadAppointments()
    {
        $userId = Auth::id();
        $todayDate = Carbon::today(); // Pega a data de hoje, à meia-noite (início do dia)

        // Status que consideramos como "finalizados" ou não mais "ativos/próximos"
        $nonActiveStatuses = ['cancelado', 'cancelado_pelo_cliente', 'concluido', 'nao_compareceu'];

        // Próximos Agendamentos:
        // - Todos os agendamentos a partir da data de HOJE (inclusive)
        // - E que NÃO estejam em um status final (cancelado, concluído, etc.)
        $this->upcomingAppointments = Appointment::with('service')
            ->where('user_id', $userId)
            ->whereDate('appointment_time', '>=', $todayDate) // A partir de hoje
            ->whereNotIn('status', $nonActiveStatuses)
            ->orderBy('appointment_time', 'asc')
            ->get();

        // Histórico de Agendamentos:
        // - Agendamentos de dias anteriores a hoje (estritamente antes de $todayDate)
        // - OU agendamentos da data de hoje que JÁ ESTÃO em um status final
        $this->pastAppointments = Appointment::with('service')
            ->where('user_id', $userId)
            ->where(function ($query) use ($todayDate, $nonActiveStatuses) {
                $query->whereDate('appointment_time', '<', $todayDate) // Dias anteriores a hoje
                      ->orWhere(function ($queryTodayFinalized) use ($todayDate, $nonActiveStatuses) {
                          $queryTodayFinalized->whereDate('appointment_time', '=', $todayDate) // Ou de hoje
                                             ->whereIn('status', $nonActiveStatuses); // Que já estão finalizados
                      });
            })
            ->orderBy('appointment_time', 'desc')
            ->take(20) // Limita o histórico para melhor performance
            ->get();
    }

    // ... (resto dos seus métodos: confirmCancellation, cancelAppointment, closeModal, render) ...
    // Certifique-se que os métodos `confirmCancellation` e `cancelAppointment` continuam usando
    // Carbon::now() para as verificações de antecedência, pois isso é relativo ao momento atual.
    // A lógica de exibição é que mudou, não a regra de negócio para cancelamento.

    public function confirmCancellation(Appointment $appointment)
    {
        $this->cancellationNotAllowedReason = '';
        $appointmentTime = Carbon::parse($appointment->appointment_time);
        $now = Carbon::now(); // Usar o momento atual para a regra de negócio

        if ($appointmentTime->lt($now) || $now->diffInHours($appointmentTime, false) < self::MIN_HOURS_BEFORE_CANCELLATION) {
            $this->cancellationNotAllowedReason = 'Não é possível cancelar agendamentos com menos de ' . self::MIN_HOURS_BEFORE_CANCELLATION . ' horas de antecedência ou que já passaram.';
            $this->appointmentToCancel = null;
            session()->flash('error', $this->cancellationNotAllowedReason);
            return;
        }
        
        if (in_array($appointment->status, ['cancelado', 'cancelado_pelo_cliente', 'concluido', 'nao_compareceu'])) {
            $this->cancellationNotAllowedReason = 'Este agendamento não pode mais ser cancelado (status: ' . $appointment->status . ').';
            $this->appointmentToCancel = null;
            session()->flash('error', $this->cancellationNotAllowedReason);
            return;
        }

        $this->appointmentToCancel = $appointment;
    }

    public function cancelAppointment()
    {
        if ($this->appointmentToCancel) {
            $appointmentTime = Carbon::parse($this->appointmentToCancel->appointment_time);
            $now = Carbon::now(); // Usar o momento atual

            if ($appointmentTime->lt($now) || $now->diffInHours($appointmentTime, false) < self::MIN_HOURS_BEFORE_CANCELLATION) {
                session()->flash('error', 'Não é mais possível cancelar este agendamento devido à proximidade do horário ou por já ter passado.');
                $this->appointmentToCancel = null;
                $this->loadAppointments();
                return;
            }
            
            if (in_array($this->appointmentToCancel->status, ['cancelado', 'cancelado_pelo_cliente', 'concluido', 'nao_compareceu'])) {
                 session()->flash('error', 'Este agendamento já se encontra em um estado final e não pode ser cancelado novamente.');
                 $this->appointmentToCancel = null;
                 $this->loadAppointments();
                 return;
            }

            $this->appointmentToCancel->status = 'cancelado_pelo_cliente';
            $this->appointmentToCancel->save();

            session()->flash('success', 'Agendamento cancelado com sucesso!');
            $this->appointmentToCancel = null;
            $this->loadAppointments();
        }
    }

    public function closeModal()
    {
        $this->appointmentToCancel = null;
        $this->cancellationNotAllowedReason = '';
    }

    public function render()
    {
        return view('livewire.client.my-appointments-list');
    }
}