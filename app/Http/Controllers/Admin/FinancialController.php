<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Expense;

class FinancialController extends Controller
{
    public function index()
    {
        // === CÁLCULOS DE RECEITA (FATURAMENTO) ===
        $paidAppointmentsQuery = Appointment::where('payment_status', 'pago');

        $revenueToday = (clone $paidAppointmentsQuery)
            ->whereDate('appointment_time', Carbon::today())
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        $revenueThisWeek = (clone $paidAppointmentsQuery)
            ->whereBetween('appointment_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        $revenueThisMonth = (clone $paidAppointmentsQuery)
            ->whereMonth('appointment_time', Carbon::now()->month)
            ->whereYear('appointment_time', Carbon::now()->year)
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        // === CÁLCULOS DE DESPESAS ===
        $expensesQuery = Expense::query();

        $expensesToday = (clone $expensesQuery)
            ->whereDate('expense_date', Carbon::today())
            ->sum('amount');

        $expensesThisWeek = (clone $expensesQuery)
            ->whereBetween('expense_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('amount');

        $expensesThisMonth = (clone $expensesQuery)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount');

        // === CÁLCULOS DE LUCRO LÍQUIDO ===
        $profitToday = $revenueToday - $expensesToday;
        $profitThisWeek = $revenueThisWeek - $expensesThisWeek;
        $profitThisMonth = $revenueThisMonth - $expensesThisMonth;


        // === LISTAS PARA A PÁGINA ===
        // Lista paginada de todas as transações de receita (pagas)
        $paidTransactions = Appointment::where('payment_status', 'pago')
            ->with(['user:id,name', 'service:id,name,price'])
            ->orderBy('appointment_time', 'desc')
            ->paginate(15, ['*'], 'revenuePage'); // Nome da página para evitar conflito de paginação

        // Lista paginada de todas as despesas
        $recentExpenses = Expense::with('category:id,name')
            ->orderBy('expense_date', 'desc')
            ->paginate(15, ['*'], 'expensesPage'); // Nome da página para evitar conflito


        return view('admin.financials.index', compact(
            'revenueToday', 'expensesToday', 'profitToday',
            'revenueThisWeek', 'expensesThisWeek', 'profitThisWeek',
            'revenueThisMonth', 'expensesThisMonth', 'profitThisMonth',
            'paidTransactions',
            'recentExpenses'
        ));
    }
}