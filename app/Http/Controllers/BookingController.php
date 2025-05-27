<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service; // Poderemos precisar para passar serviços, se o Livewire não buscar

class BookingController extends Controller
{
    /**
     * Exibe a página principal de agendamento.
     */
    public function index()
    {
        // A view principal que hospedará o componente Livewire de agendamento.
        return view('booking.index');
    }
}