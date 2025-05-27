<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do serviço
            $table->decimal('price', 8, 2); // Preço, ex: 150.00 (8 dígitos no total, 2 após a vírgula)
            $table->integer('duration_minutes'); // Duração em minutos
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};