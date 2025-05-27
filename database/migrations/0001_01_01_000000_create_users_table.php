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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Coluna ID auto-incremento e chave primária
            $table->string('name'); // Nome do usuário
            $table->string('email')->unique(); // Email único
            $table->timestamp('email_verified_at')->nullable(); // Para verificação de email
            $table->string('password'); // Senha (será armazenada com hash)
            $table->boolean('is_admin')->default(false); // Para identificar administradores
            $table->rememberToken(); // Para funcionalidade "lembrar-me"
            $table->timestamps(); // Colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
