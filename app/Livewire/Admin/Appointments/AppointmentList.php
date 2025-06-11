<?php

namespace App\Livewire\Admin\Appointments;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

class AppointmentList extends Component
{
    use WithPagination;

    // Propriedades para os filtros da view
    public string $search = '';
    public string $filterStatus = '';
    public string $filterDate = '';

    // Propriedades para o modal de cancelamento
    public bool $showCancelModal = false;
    public ?int $appointmentIdToCancel = null;

    public function render()
    {
        $query = Appointment::query()
            // Carrega os relacionamentos para evitar o problema N+1
            ->with(['user', 'service']);

        // Filtro de busca pelo nome do cliente
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por status do agendamento
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        
        // Filtro por data
        if ($this->filterDate) {
            $query->whereDate('appointment_time', $this->filterDate);
        }

        $appointments = $query->latest('appointment_time')->paginate(10);

        return view('livewire.admin.appointments.appointment-list', [
            'appointments' => $appointments,
        ]);
    }

    // --- MÉTODOS PARA RESETAR A PAGINAÇÃO ---
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }

    // --- MÉTODOS DE AÇÃO DO DROPDOWN ---

    public function confirmAppointment(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmado']);
        session()->flash('message', 'Agendamento confirmado com sucesso!');
    }

    public function markAsCompleted(Appointment $appointment)
    {
        $appointment->update(['status' => 'concluido']);
        session()->flash('message', 'Agendamento marcado como concluído!');
    }

    public function confirmCancellation($appointmentId)
    {
        $this->appointmentIdToCancel = $appointmentId;
        $this->showCancelModal = true;
    }

    public function cancelAppointment()
    {
        if ($this->appointmentIdToCancel) {
            $appointment = Appointment::find($this->appointmentIdToCancel);
            if ($appointment) {
                $appointment->update(['status' => 'cancelado']);
                session()->flash('message', 'Agendamento cancelado com sucesso.');
            }
        }
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showCancelModal = false;
        $this->reset('appointmentIdToCancel');
    }
}