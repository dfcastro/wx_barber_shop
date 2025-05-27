<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Chave estrangeira para users
            $table->foreignId('service_id')->constrained()->onDelete('cascade'); // Chave estrangeira para services
            $table->dateTime('appointment_time'); // Data e hora do agendamento
            $table->string('status')->default('pendente'); // Ex: pendente, confirmado, cancelado, concluido
            $table->text('notes')->nullable(); // Observações opcionais
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};