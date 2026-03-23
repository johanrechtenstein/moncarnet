<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Maintenance;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Maintenance::create([
        'date' => now(),
        'kilometrage' => 150000,
        'description' => 'Vidange moteur et filtre à huile',
        'categorie_id' => 1, 
        'car_id' => 1,
    ]);
    }
}
