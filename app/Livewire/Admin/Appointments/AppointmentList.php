<?php

namespace App\Livewire\Admin\Appointments;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;
use App\Mail\BookingConfirmedToClient;
use App\Mail\BookingCancelledByAdminToClient;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AppointmentList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public $filterDate;

    // ... (métodos confirmAppointment e cancelAppointmentAsAdmin como antes) ...
    public function confirmAppointment(Appointment $appointment)
    {
        $appointment->status = 'confirmado';
        $appointment->save();

        try {
            Mail::to($appointment->user->email)->queue(new BookingConfirmedToClient($appointment));
        } catch (\Exception $e) {
            Log::error("Erro ao enfileirar e-mail de confirmação para agendamento ID {$appointment->id}: " . $e->getMessage());
        }

        session()->flash('message', 'Agendamento confirmado com sucesso!');
    }

    public function cancelAppointmentAsAdmin(Appointment $appointment)
    {
        $appointment->status = 'cancelado';
        $appointment->save();

        try {
            Mail::to($appointment->user->email)->queue(new BookingCancelledByAdminToClient($appointment));
        } catch (\Exception $e) {
            Log::error("Erro ao enfileirar e-mail de cancelamento para agendamento ID {$appointment->id}: " . $e->getMessage());
        }

        session()->flash('message', 'Agendamento cancelado com sucesso!');
    }


    /**
     * Marca um agendamento como pago.
     *
     * @param int $appointmentId
     * @return void
     */
    public function markAsPaid(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if ($appointment) {
            // Decisão: Ao marcar como pago, também marcamos como concluído?
            // Se sim, descomente a linha abaixo. Faz sentido que um agendamento pago
            // tenha seu ciclo de vida concluído.
            $appointment->status = 'concluido';
            
            $appointment->payment_status = 'pago';
            // Por enquanto, não vamos definir um método de pagamento específico aqui para manter simples.
            // Poderíamos abrir um modal para o admin escolher o método.
            // $appointment->payment_method = 'dinheiro'; // Exemplo
            $appointment->save();

            session()->flash('message', 'Agendamento ID #' . $appointment->id . ' marcado como pago e concluído.');
            Log::info("Admin (ID: " . auth()->id() . ") marcou o agendamento ID {$appointment->id} como pago.");
        } else {
            session()->flash('error', 'Agendamento não encontrado.');
        }
    }


    public function render()
    {
        $query = Appointment::with(['user', 'service'])->orderBy('appointment_time', 'desc');

        if ($this->filterDate) {
            $query->whereDate('appointment_time', $this->filterDate);
        }

        $appointments = $query->paginate(10);

        return view('livewire.admin.appointments.appointment-list', [
            'appointments' => $appointments
        ]);
    }
}