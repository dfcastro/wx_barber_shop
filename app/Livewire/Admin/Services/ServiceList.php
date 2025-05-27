<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service; // Importe o Model Service
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class ServiceList extends Component
{
    public Collection $services;

    public function mount(Collection $services)
    {
        $this->services = $services;
    }

    // NOVO MÉTODO PARA DELETAR O SERVIÇO
    public function deleteService($serviceId)
    {
        $service = Service::find($serviceId);

        if ($service) {
            $service->delete();
            session()->flash('success', 'Serviço deletado com sucesso!');
            // Atualiza a lista de serviços após a exclusão
            // Isso é importante porque a coleção original foi passada via mount
            // e não será automaticamente atualizada apenas pela exclusão no BD.
            $this->services = Service::orderBy('name')->get();
        } else {
            session()->flash('error', 'Erro ao tentar deletar o serviço. Não encontrado.');
        }
        // O Livewire irá re-renderizar o componente automaticamente após a execução deste método.
    }

    public function render()
    {
        return view('livewire.admin.services.service-list');
    }
}