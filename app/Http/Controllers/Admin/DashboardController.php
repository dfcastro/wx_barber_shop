<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// Se o seu controller não tiver o "use Illuminate\Http\Request", não tem problema.
// Ele não é necessário para o método index simples.

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lógica para calcular os ganhos totais (esta parte já estava correta)
        $totalRevenue = \App\Models\Appointment::where('payment_status', 'paid')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');
        
        $totalAppointments = \App\Models\Appointment::count();
        
        $newClientsThisMonth = \App\Models\User::where('is_admin', false)
                                   ->whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count();
    
        $totalExpenses = \App\Models\Expense::sum('amount');
        
        // --- INÍCIO DA CORREÇÃO FINAL ---
        // A consulta agora usa a coluna correta: 'appointment_time'
        $upcomingAppointments = \App\Models\Appointment::with(['user', 'service'])
                                            ->where('appointment_time', '>=', now())
                                            ->orderBy('appointment_time', 'asc')
                                            ->take(5)
                                            ->get();
        // --- FIM DA CORREÇÃO FINAL ---
    
        return view('admin.dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalAppointments' => $totalAppointments,
            'newClientsThisMonth' => $newClientsThisMonth,
            'totalExpenses' => $totalExpenses,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}