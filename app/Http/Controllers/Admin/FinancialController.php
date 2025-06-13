<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Expense;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        // Pega as datas do request, ou define um padrão (ex: este mês)
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfMonth();

        // --- CÁLCULO DOS GANHOS (REVENUE) ---
        // Busca agendamentos pagos dentro do período selecionado
        $paidAppointmentsQuery = Appointment::where('payment_status', 'paid')
            ->whereBetween('appointment_time', [$startDate, $endDate]);

        // Clona a query para buscar os detalhes para a tabela
        $paidAppointments = (clone $paidAppointmentsQuery)
            ->with(['user', 'service'])
            ->latest('appointment_time')
            ->get();
            
        // Calcula o total dos ganhos juntando com a tabela de serviços
        $totalRevenue = (clone $paidAppointmentsQuery)
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');


        // --- CÁLCULO DAS DESPESAS (EXPENSES) ---
        // Busca despesas dentro do período selecionado
        $expensesQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);

        // Clona a query para buscar os detalhes para a tabela
        $expenses = (clone $expensesQuery)
            ->latest('expense_date')
            ->get();
            
        // Calcula o total das despesas
        $totalExpenses = (clone $expensesQuery)->sum('amount');
        

        // --- CÁLCULO DO LUCRO LÍQUIDO ---
        $netProfit = $totalRevenue - $totalExpenses;


        // --- ENVIO DOS DADOS PARA A VIEW ---
        return view('admin.financials.index', [
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'paidAppointments' => $paidAppointments,
            'expenses' => $expenses,
        ]);
    }
}