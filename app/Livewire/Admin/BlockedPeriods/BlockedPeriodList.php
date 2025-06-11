<?php

namespace App\Livewire\Admin\BlockedPeriods;

use App\Models\BlockedPeriod;
use Livewire\Component;
use Livewire\WithPagination;

class BlockedPeriodList extends Component
{
    use WithPagination;

    public ?int $periodToDeleteId = null;

    public function render()
    {
        $blockedPeriods = BlockedPeriod::latest('start_datetime')->paginate(10);

        return view('livewire.admin.blocked-periods.blocked-period-list', [
            'blockedPeriods' => $blockedPeriods,
        ]);
    }

    /**
     * Prepara a deleção e despacha um evento para abrir o modal no frontend.
     */
    public function confirmPeriodDeletion($periodId)
    {
        $this->periodToDeleteId = $periodId;
        
        // A MUDANÇA ESTÁ AQUI: Despachamos um evento chamado 'open-modal'
        // com o valor 'confirm-period-deletion', que é o NOME do nosso modal.
        $this->dispatch('open-modal', 'confirm-period-deletion');
    }

    /**
     * Deleta o período selecionado.
     */
    public function deletePeriod()
    {
        if ($this->periodToDeleteId) {
            $period = BlockedPeriod::find($this->periodToDeleteId);
            if ($period) {
                $period->delete();
                session()->flash('message', 'Período bloqueado deletado com sucesso!');
            }
        }
        
        // O modal irá fechar-se sozinho na view, apenas resetamos o ID.
        $this->reset('periodToDeleteId');
    }
}