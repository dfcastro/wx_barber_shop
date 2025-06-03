<?php

namespace App\Livewire\Admin\Clients;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;

class ClientList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public string $search = '';

    // Novas propriedades para os filtros
    public string $filterAccountStatus = ''; // Opções: '', 'active', 'inactive'
    public string $filterEmailVerified = ''; // Opções: '', 'verified', 'unverified'

    // Hooks para resetar a paginação quando os filtros ou a busca mudam
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterAccountStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterEmailVerified()
    {
        $this->resetPage();
    }

    // ... (métodos toggleAccountStatus, markAsVerified, sendPasswordResetLink como antes) ...
    public function toggleAccountStatus(int $userId)
    {
        $user = User::find($userId);
        if ($user && !$user->is_admin) {
            $user->is_active = !$user->is_active;
            $user->save();
            $action = $user->is_active ? 'ativada' : 'desativada';
            session()->flash('message', "Conta do cliente {$user->name} foi {$action} com sucesso.");
            Log::info("Admin (ID: " . auth()->id() . ") alterou o status da conta do usuário ID {$user->id} para {$action}.");
        } else {
            session()->flash('error', 'Não foi possível alterar o status da conta ou o usuário é um administrador.');
            Log::warning("Admin (ID: " . auth()->id() . ") tentou alterar o status de um usuário inexistente ou administrador (ID: {$userId}).");
        }
    }

    public function markAsVerified(int $userId)
    {
        $user = User::find($userId);
        if ($user && !$user->is_admin) {
            if (!$user->hasVerifiedEmail()) {
                $user->email_verified_at = Carbon::now();
                $user->save();
                session()->flash('message', "E-mail do cliente {$user->name} marcado como verificado com sucesso.");
                Log::info("Admin (ID: " . auth()->id() . ") marcou o e-mail do usuário ID {$user->id} como verificado.");
            } else {
                session()->flash('info', "O e-mail do cliente {$user->name} já estava verificado.");
            }
        } else {
            session()->flash('error', 'Não foi possível marcar o e-mail como verificado ou o usuário é um administrador.');
            Log::warning("Admin (ID: " . auth()->id() . ") tentou marcar e-mail de usuário inexistente ou administrador (ID: {$userId}) como verificado.");
        }
    }

    public function sendPasswordResetLink(int $userId)
    {
        $user = User::find($userId);
        if ($user && !$user->is_admin) {
            if ($user->password && $user->hasVerifiedEmail()) {
                try {
                    $broker = Password::broker();
                    $status = $broker->sendResetLink(['email' => $user->email]);
                    if ($status == Password::RESET_LINK_SENT) {
                        session()->flash('message', 'Link de redefinição de senha enviado para ' . $user->email);
                        Log::info("Admin (ID: " . auth()->id() . ") enviou link de reset de senha para usuário ID {$user->id} ({$user->email}).");
                    } else {
                        session()->flash('error', 'Não foi possível enviar o link de redefinição de senha: ' . __($status));
                        Log::error("Admin (ID: " . auth()->id() . ") falhou ao enviar link de reset para ID {$user->id}. Status: " . $status);
                    }
                } catch (\Exception $e) {
                    session()->flash('error', 'Ocorreu um erro ao tentar enviar o link de redefinição de senha.');
                    Log::error("Admin (ID: " . auth()->id() . ") - Exceção ao enviar link de reset para ID {$user->id}: " . $e->getMessage());
                }
            } elseif (!$user->hasVerifiedEmail()) {
                session()->flash('error', "O e-mail de {$user->name} precisa ser verificado antes de enviar um link de reset.");
            } else {
                session()->flash('info', "Este usuário ({$user->name}) provavelmente usa login social ou não tem uma senha configurada para reset.");
            }
        } else {
            session()->flash('error', 'Usuário não encontrado ou é um administrador.');
        }
    }


    public function render()
    {
        $clientsQuery = User::where('is_admin', false); // Apenas clientes

        // Aplicar filtro de busca
        if (!empty($this->search)) {
            $clientsQuery->where(function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where('name', 'like', $searchTerm)
                      ->orWhere('email', 'like', $searchTerm);
            });
        }

        // Aplicar filtro de Status da Conta
        if ($this->filterAccountStatus === 'active') {
            $clientsQuery->where('is_active', true);
        } elseif ($this->filterAccountStatus === 'inactive') {
            $clientsQuery->where('is_active', false);
        }

        // Aplicar filtro de E-mail Verificado
        if ($this->filterEmailVerified === 'verified') {
            $clientsQuery->whereNotNull('email_verified_at');
        } elseif ($this->filterEmailVerified === 'unverified') {
            $clientsQuery->whereNull('email_verified_at');
        }

        $clients = $clientsQuery->orderBy('name', 'asc')->paginate(10);

        return view('livewire.admin.clients.client-list', [
            'clients' => $clients,
        ]);
    }
}