<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Card 1: Agendamentos para Hoje (pendentes ou confirmados)
        $appointmentsTodayCount = Appointment::whereIn('status', ['pendente', 'confirmado'])
            ->whereDate('appointment_time', Carbon::today())
            ->count();

        // Card 2: Faturamento do Mês (soma do preço de agendamentos concluídos no mês atual)
        $revenueThisMonth = Appointment::where('status', 'concluido')
            ->whereMonth('appointment_time', Carbon::now()->month)
            ->whereYear('appointment_time', Carbon::now()->year)
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        // Card 3: Total de Clientes (não-admins)
        $totalClientsCount = User::where('is_admin', false)->count();

        // Card 4: Novos Clientes (nos últimos 30 dias)
        $newClientsCount = User::where('is_admin', false)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
            
        // Lista: Próximos 5 Agendamentos (pendentes ou confirmados)
        $upcomingAppointments = Appointment::whereIn('status', ['pendente', 'confirmado'])
            ->where('appointment_time', '>=', Carbon::now())
            ->with(['user:id,name', 'service:id,name']) // Carrega nome do cliente e do serviço para otimizar
            ->orderBy('appointment_time', 'asc')
            ->limit(5)
            ->get();


        return view('admin.dashboard', compact(
            'appointmentsTodayCount',
            'revenueThisMonth',
            'totalClientsCount',
            'newClientsCount',
            'upcomingAppointments'
        ));
    }
}
