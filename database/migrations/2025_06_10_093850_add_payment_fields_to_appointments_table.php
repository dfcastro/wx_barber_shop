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
        Schema::table('appointments', function (Blueprint $table) {
            // Adiciona o status do pagamento, com 'pendente' como padrão
            $table->string('payment_status')->default('pendente')->after('status');
            
            // Adiciona o método de pagamento, que pode ser nulo
            $table->string('payment_method')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_method']);
        });
    }
};