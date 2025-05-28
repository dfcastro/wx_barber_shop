<?php

namespace App\Livewire\Admin\Appointments; // Verifique o namespace

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination; // Para paginação no futuro

class AppointmentList extends Component
{
    use WithPagination; // Habilita a paginação do Livewire

    public $filterDate;
    public $filterStatus = ''; // Todos, pendente, confirmado, cancelado, etc.
    public $searchClient;

    protected $paginationTheme = 'tailwind'; // Usa o tema do Tailwind para paginação

    public function approveAppointment(Appointment $appointment)
    {
        $appointment->status = 'confirmado';
        $appointment->save();
        session()->flash('success', 'Agendamento aprovado com sucesso!');
    }

    public function cancelAppointment(Appointment $appointment)
    {
        $appointment->status = 'cancelado';
        $appointment->save();
        session()->flash('success', 'Agendamento cancelado com sucesso!');
        // Aqui você pode adicionar lógica para notificar o cliente, etc.
    }

    public function render()
    {
        $query = Appointment::with(['user', 'service']) // Carrega os relacionamentos para evitar N+1 queries
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

        $appointments = $query->paginate(10); // Pagina com 10 itens por página

        return view('livewire.admin.appointments.appointment-list', [
            'appointments' => $appointments,
        ]);
    }
}