<?php

namespace App\Livewire\Admin\Clients;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log; // Para logs

class ClientList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Alterna o status 'is_active' de um usuário.
     *
     * @param int $userId
     * @return void
     */
    public function toggleAccountStatus(int $userId)
    {
        $user = User::find($userId);

        if ($user && !$user->is_admin) { // Garante que não está alterando um admin e que o usuário existe
            $user->is_active = !$user->is_active;
            $user->save();

            $action = $user->is_active ? 'ativada' : 'desativada';
            session()->flash('message', "Conta do cliente {$user->name} foi {$action} com sucesso.");
            Log::info("Admin (ID: " . auth()->id() . ") alterou o status da conta do usuário ID {$user->id} para {$action}.");
        } else {
            session()->flash('error', 'Não foi possível alterar o status da conta ou o usuário é um administrador.');
            Log::warning("Admin (ID: " . auth()->id() . ") tentou alterar o status de um usuário inexistente ou administrador (ID: {$userId}).");
        }

        // Não é necessário chamar $this->render() explicitamente, o Livewire re-renderiza
        // automaticamente após uma ação que altera propriedades públicas ou após uma ação pública.
        // Se a lista não atualizar, você pode precisar forçar um refresh dos dados.
    }

    public function render()
    {
        $clientsQuery = User::where('is_admin', false);

        if (!empty($this->search)) {
            $clientsQuery->where(function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where('name', 'like', $searchTerm)
                      ->orWhere('email', 'like', $searchTerm);
            });
        }

        $clients = $clientsQuery->orderBy('name', 'asc')
                                ->paginate(10);

        return view('livewire.admin.clients.client-list', [
            'clients' => $clients,
        ]);
    }
}