<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FinancialController extends Controller
{
    public function index()
    {
        // Base query para agendamentos pagos
        $paidAppointmentsQuery = Appointment::where('payment_status', 'pago');

        // Faturamento do Dia
        $revenueToday = (clone $paidAppointmentsQuery) // Clona a query para não afetar as outras
            ->whereDate('appointment_time', Carbon::today())
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        // Faturamento da Semana (de Segunda a Domingo)
        $revenueThisWeek = (clone $paidAppointmentsQuery)
            ->whereBetween('appointment_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        // Faturamento do Mês
        $revenueThisMonth = (clone $paidAppointmentsQuery)
            ->whereMonth('appointment_time', Carbon::now()->month)
            ->whereYear('appointment_time', Carbon::now()->year)
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        // Lista paginada de todas as transações pagas
        $paidTransactions = Appointment::where('payment_status', 'pago')
            ->with(['user:id,name', 'service:id,name,price']) // Carrega nome do cliente e dados do serviço
            ->orderBy('appointment_time', 'desc')
            ->paginate(15); // Pagina a cada 15 registros

        return view('admin.financials.index', compact(
            'revenueToday',
            'revenueThisWeek',
            'revenueThisMonth',
            'paidTransactions'
        ));
    }
}