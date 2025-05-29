<?php

namespace App\Livewire\Admin\Appointments;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // << Adicione este import
use App\Mail\BookingConfirmedToClient; // << Adicione este import
use App\Mail\BookingCancelledByAdminToClient; // << Adicione este import

class AppointmentList extends Component
{
    use WithPagination;

    public $filterDate;
    public $filterStatus = '';
    public $searchClient;

    protected $paginationTheme = 'tailwind';

    public function approveAppointment(Appointment $appointment)
    {
        $appointment->status = 'confirmado';
        $appointment->save();

        // Enviar e-mail de confirmação para o cliente
        try {
            Mail::to($appointment->user->email)->send(new BookingConfirmedToClient($appointment));
            Log::info("E-mail de confirmação enviado para: " . $appointment->user->email . " para o agendamento ID: " . $appointment->id);
        } catch (\Exception $e) {
            Log::error("Erro ao enviar e-mail de confirmação para agendamento ID {$appointment->id}: " . $e->getMessage());
        }

        session()->flash('success', 'Agendamento aprovado com sucesso e cliente notificado!');
    }

    public function cancelAppointment(Appointment $appointment)
    {
        $appointment->status = 'cancelado';
        // $cancellationReason = "Motivo do admin aqui"; // No futuro, pegar de um input
        $appointment->save();

        // Enviar e-mail de cancelamento para o cliente
        try {
            Mail::to($appointment->user->email)->send(new BookingCancelledByAdminToClient($appointment /*, $cancellationReason */));
            Log::info("E-mail de cancelamento (pelo admin) enviado para: " . $appointment->user->email . " para o agendamento ID: " . $appointment->id);
        } catch (\Exception $e) {
            Log::error("Erro ao enviar e-mail de cancelamento (pelo admin) para agendamento ID {$appointment->id}: " . $e->getMessage());
        }

        session()->flash('success', 'Agendamento cancelado com sucesso e cliente notificado!');
    }

    public function render()
    {
        // ... (lógica de renderização existente) ...
        Log::info("Admin AppointmentList: filterDate='{$this->filterDate}', filterStatus='{$this->filterStatus}', searchClient='{$this->searchClient}'");

        $query = Appointment::with(['user', 'service'])
                            ->orderBy('appointment_time', 'desc');

        if ($this->filterDate) {
            $query->whereDate('appointment_time', $this->filterDate);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->searchClient) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->searchClient . '%')
                  ->orWhere('email', 'like', '%' . $this->searchClient . '%');
            });
        }
        
        $appointments = $query->paginate(10);
        Log::info("Admin AppointmentList: Fetched " . $appointments->count() . " appointments for display. Total " . $appointments->total());


        return view('livewire.admin.appointments.appointment-list', [
            'appointments' => $appointments,
        ]);
    }
}