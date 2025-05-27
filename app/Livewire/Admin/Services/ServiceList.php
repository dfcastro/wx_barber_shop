<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection; // Ainda podemos usar para type hint

class ServiceList extends Component
{
    public Collection $services; // Continuaremos usando esta propriedade
    public ?Service $serviceToDelete = null; // Para o modal de confirmação

    // O método mount não é mais necessário para receber $services
    // public function mount(Collection $services)
    // {
    //     $this->services = $services;
    // }

    public function confirmDeletion(Service $service)
    {
        $this->serviceToDelete = $service;
    }

    public function deleteService()
    {
        if ($this->serviceToDelete) {
            $this->serviceToDelete->delete();
            session()->flash('success', 'Serviço excluído com sucesso!');
            $this->serviceToDelete = null; // Reseta para fechar o modal
            // A lista será atualizada no próximo render()
        }
    }

    public function cancelDelete()
    {
        $this->serviceToDelete = null;
    }

    public function render()
    {
        // Busca os serviços aqui, toda vez que o componente renderizar
        $this->services = Service::orderBy('name')->get();
        return view('livewire.admin.services.service-list');
    }
}