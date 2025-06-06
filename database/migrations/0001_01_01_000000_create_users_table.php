<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // Em database/migrations/0001_01_01_000000_create_users_table.php
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('phone_number')->nullable(); // << VERIFIQUE SE ESTA LINHA EXISTE E É 'phone_number'
        $table->boolean('is_admin')->default(false);
        // ... outras colunas como is_active, provider_name, provider_id ...
        $table->rememberToken();
        $table->timestamps();
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
