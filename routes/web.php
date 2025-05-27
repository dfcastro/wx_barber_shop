<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ServiceController; 

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
});

require __DIR__.'/auth.php';
