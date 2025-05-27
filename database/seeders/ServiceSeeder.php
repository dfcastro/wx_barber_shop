<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service; 


class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        Service::create(['name' => 'Corte (todo em 1 máquina)', 'price' => 32.00, 'duration_minutes' => 30]);
        Service::create(['name' => 'Corte (infantil)', 'price' => 30.00, 'duration_minutes' => 30]);
        Service::create(['name' => 'Corte', 'price' => 40.00, 'duration_minutes' => 40]);
        Service::create(['name' => 'Barba', 'price' => 25.00, 'duration_minutes' => 20]);
        Service::create(['name' => 'Barboterapia', 'price' => 45.00, 'duration_minutes' => 60]);
        Service::create(['name' => 'Sobrancelha', 'price' => 12.00, 'duration_minutes' => 5]);
        Service::create(['name' => 'Progressiva (a partir de)', 'price' => 110.00, 'duration_minutes' => 90]);
        Service::create(['name' => 'Relaxamento (a partir de)', 'price' => 40.00, 'duration_minutes' => 30]);
        Service::create(['name' => 'Escova', 'price' => 25.00, 'duration_minutes' => 30]);
        Service::create(['name' => 'Pezinho', 'price' => 15.00, 'duration_minutes' => 10]);
        Service::create(['name' => 'Hidratação', 'price' => 25.00, 'duration_minutes' => 30]);
        Service::create(['name' => 'Hidratação + escova', 'price' => 45.00, 'duration_minutes' => 45]);
        Service::create(['name' => 'Ozonioterapia capilar', 'price' => 40.00, 'duration_minutes' => 40]);
        Service::create(['name' => 'Coloração', 'price' => 35.00, 'duration_minutes' => 40]);
        Service::create(['name' => 'Finalização nudred', 'price' => 30.00, 'duration_minutes' => 30]);
    }
}
