<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceList extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $serviceIdToDelete = null;

    // A propriedade $showDeleteModal não é mais necessária aqui.
    
    public function render()
    {
        $query = Service::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $services = $query->latest('id')->paginate(10);

        return view('livewire.admin.services.service-list', [
            'services' => $services,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Prepara a deleção e despacha um evento para abrir o modal no frontend.
     */
    public function confirmServiceDeletion($serviceId)
    {
        $this->serviceIdToDelete = $serviceId;
        // A MUDANÇA ESTÁ AQUI: Despachamos um evento para o navegador.
        $this->dispatch('open-delete-modal');
    }

    /**
     * Deleta o serviço selecionado.
     */
    public function deleteService()
    {
        if ($this->serviceIdToDelete) {
            $service = Service::find($this->serviceIdToDelete);
            if ($service) {
                $service->delete();
                session()->flash('message', 'Serviço deletado com sucesso!');
            }
        }
        
        $this->reset('serviceIdToDelete');
    }
}