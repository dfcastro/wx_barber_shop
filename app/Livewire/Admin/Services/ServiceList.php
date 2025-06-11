<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public bool $showDeleteModal = false;

    // Propriedade para controlar o modal de confirmação
    public ?int $serviceIdToDelete = null;

    public function render()
    {
        $query = Service::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $isActive = ($this->filterStatus === 'active');
            $query->where('is_active', $isActive);
        }

        $services = $query->latest()->paginate(10);

        return view('livewire.admin.services.service-list', [
            'services' => $services,
        ]);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    /**
     * Prepara para a deleção: guarda o ID e define a flag para mostrar o modal.
     */
    public function confirmServiceDeletion($serviceId)
    {
        $this->serviceIdToDelete = $serviceId;
        $this->showDeleteModal = true;
    }

    /**
     * Deleta o serviço e reseta as propriedades para fechar o modal.
     */
    public function deleteService()
    {
        if ($this->serviceIdToDelete) {
            Service::find($this->serviceIdToDelete)->delete();
            session()->flash('message', 'Serviço deletado com sucesso!');
        }
        $this->showDeleteModal = false;
        $this->reset('serviceIdToDelete');
    }

    /**
     * Função para fechar o modal sem deletar.
     */
    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->reset('serviceIdToDelete');
    }
}