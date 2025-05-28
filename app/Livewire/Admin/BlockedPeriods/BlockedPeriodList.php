<?php

namespace App\Livewire\Admin\BlockedPeriods; // Verifique o namespace

use App\Models\BlockedPeriod;
use Livewire\Component;
use Livewire\WithPagination;

class BlockedPeriodList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public ?BlockedPeriod $periodToDelete = null;

    public function confirmDeletion(BlockedPeriod $blockedPeriod)
    {
        $this->periodToDelete = $blockedPeriod;
    }

    public function deleteBlockedPeriod()
    {
        if ($this->periodToDelete) {
            $this->periodToDelete->delete();
            session()->flash('success', 'Período bloqueado excluído com sucesso!');
            $this->periodToDelete = null; // Reseta para fechar o modal e força re-render
        }
    }

    public function cancelDelete()
    {
        $this->periodToDelete = null;
    }

    public function render()
    {
        $blockedPeriods = BlockedPeriod::orderBy('start_datetime', 'desc')->paginate(10);

        return view('livewire.admin.blocked-periods.blocked-period-list', [
            'blockedPeriods' => $blockedPeriods,
        ]);
    }
}