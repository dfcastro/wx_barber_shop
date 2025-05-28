<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para pegar o usuário logado

class ClientAppointmentController extends Controller
{
    /**
     * Exibe a página de "Meus Agendamentos" para o cliente logado.
     */
    public function index()
    {
        // A view hospedará o componente Livewire que lista os agendamentos do cliente.
        return view('client.appointments.index');
    }
}