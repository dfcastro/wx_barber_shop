<?php

namespace App\Livewire\Admin\Appointments;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

class AppointmentList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = ''; // <-- Recebe 'pendente', 'confirmado', etc.
    public string $filterDate = '';

    public function updateStatus($appointmentId, $status)
    {
        // Valida se o status enviado é um dos permitidos
        if (!in_array($status, ['confirmado', 'concluido', 'cancelado'])) {
            return;
        }
        
        $appointment = Appointment::find($appointmentId);
        if ($appointment) {
            $appointment->status = $status;
            $appointment->save();
            session()->flash('message', 'Agendamento atualizado com sucesso!');
        }
    }

    public function render()
    {
        $query = Appointment::query()->with(['user', 'service']);

        // Filtra pelo status em português
        $query->when($this->filterStatus, function ($q) {
            $q->where('status', $this->filterStatus);
        });

        // (O restante da lógica de filtro por data e busca já está correta)
        $query->when($this->filterDate, function ($q) {
            $q->whereDate('appointment_time', $this->filterDate);
        });

        $query->when($this->search, function ($q) {
            $q->where(function ($subQuery) {
                $subQuery->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('service', function ($serviceQuery) {
                    $serviceQuery->where('name', 'like', '%' . $this->search . '%');
                });
            });
        });

        $appointments = $query->latest('appointment_time')->paginate(10);

        return view('livewire.admin.appointments.appointment-list', [
            'appointments' => $appointments,
        ]);
    }
}