<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // \App\Models\User::factory(10)->create(); // Exemplo para criar usuários fakes

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            ServiceSeeder::class, // Adicione esta linha
            // Outros seeders podem ser adicionados aqui
        ]);
    }
}
