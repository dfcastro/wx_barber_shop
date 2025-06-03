<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AppointmentManagementController;
use App\Http\Controllers\Admin\BlockedPeriodController;
use App\Http\Controllers\ClientAppointmentController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Admin\ClientController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Grupo de Rotas do Administrador
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Rota para a lista de serviços (Index)
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

    // Rota para exibir o formulário de criação de serviço (Create)
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');

    // Rota para salvar um novo serviço (Store)
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');

    // Rota para exibir um serviço específico (Show) - Opcional para este CRUD simples
    // Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

    // Rota para exibir o formulário de edição de serviço (Edit)
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');

    // Rota para atualizar um serviço existente (Update)
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');

    // Rota para deletar um serviço (Destroy)
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // Você também poderia usar Route::resource se preferir uma sintaxe mais curta:
    // Route::resource('services', ServiceController::class);
    // Apenas certifique-se que os nomes das rotas e métodos do controller batam

    // Rotas para Gerenciamento de Agendamentos
    Route::get('/appointments', [AppointmentManagementController::class, 'index'])->name('appointments.index');
    // Para as ações de aprovar e cancelar, usaremos métodos POST ou PATCH/PUT via Livewire ou formulários específicos.
    // Por enquanto, a listagem é o principal. As ações podem ser métodos no componente Livewire
    // que chamam o controller ou atualizam diretamente.
    // Route::post('/appointments/{appointment}/approve', [AppointmentManagementController::class, 'approve'])->name('appointments.approve');
    // Route::post('/appointments/{appointment}/cancel', [AppointmentManagementController::class, 'cancel'])->name('appointments.cancel');

    // Rotas para Dias de Folga / Períodos Bloqueados
    Route::resource('blocked-periods', BlockedPeriodController::class)->except(['show']);
    // Usamos except(['show']) porque geralmente não precisamos de uma página separada para "mostrar um único período bloqueado".
    // A listagem (index), criação (create/store) e edição (edit/update/destroy) são suficientes.

    // Rota para Gerenciamento de Clientes
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');

    //showclients
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show'); 

    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
});

// Rota para a página de agendamento - requer autenticação
Route::middleware(['auth'])->group(function () {
    Route::get('/agendar', [BookingController::class, 'index'])
        ->middleware(['auth', 'verified', 'profile.completed']) // Adicionado 'profile.completed'
        ->name('booking.index');
    //meus agendamentos cliente
    Route::get('/meus-agendamentos', [ClientAppointmentController::class, 'index'])->name('client.appointments.index');


});

Route::middleware(['auth', 'verified', 'profile.completed'])->group(function () {
    // Rota para "Meus Agendamentos" do cliente
    Route::get('/my-appointments', [ClientAppointmentController::class, 'index'])->name('client.appointments.index');
    // Outras rotas do cliente...
});

// Rotas para Login Social
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirectToProvider'])
    ->name('socialite.redirect');

Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback'])
    ->name('socialite.callback');

require __DIR__ . '/auth.php';
