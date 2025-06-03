<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Importar o modelo User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para verificar se o admin está logado

class AdminBookingController extends Controller
{
    /**
     * Exibe a interface de agendamento para um cliente específico.
     *
     * @param  \App\Models\User $client
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createForClient(User $client)
    {
        // Opcional: Verificar se o usuário logado é realmente um admin,
        // embora o middleware de rota já deva ter cuidado disso.
        if (!Auth::user()->is_admin) {
            abort(403, 'Acesso não autorizado.');
        }

        // Garante que não estamos tentando agendar para outro administrador
        if ($client->is_admin) {
            return redirect()->route('admin.clients.index')->with('error', 'Não é possível criar agendamentos para administradores por esta interface.');
        }

        // Passa o ID do cliente para a view que carrega o componente Livewire
        return view('booking.index', ['targetClientId' => $client->id]);
    }
}