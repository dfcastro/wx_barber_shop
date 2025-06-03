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
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_name')->nullable()->after('password'); // Ou depois de outra coluna relevante
            $table->string('provider_id')->nullable()->after('provider_name');
            $table->string('provider_avatar')->nullable()->after('provider_id'); // Opcional

            // Adicionar um índice para provider_id pode ser útil
            $table->index(['provider_name', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['provider_name', 'provider_id']); // Remove o índice se o adicionou
            $table->dropColumn(['provider_name', 'provider_id', 'provider_avatar']);
        });
    }
};