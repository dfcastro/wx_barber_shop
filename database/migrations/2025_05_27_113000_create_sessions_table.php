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
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index(); // Armazena o ID do usuário logado, se houver
            $table->string('ip_address', 45)->nullable();     // Endereço IP do usuário
            $table->text('user_agent')->nullable();           // Informações do navegador do usuário
            $table->longText('payload');                      // Dados da sessão serializados
            $table->integer('last_activity')->index();        // Timestamp da última atividade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};