<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blocked_periods', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_datetime'); // Data e hora de início do bloqueio
            $table->dateTime('end_datetime');   // Data e hora de fim do bloqueio
            $table->string('reason')->nullable(); // Motivo do bloqueio (opcional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_periods');
    }
};