<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Car;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Car::create([
            'marque' => 'Peugeot',
            'modele' => '208',
            'immatriculation' => 'AA-123-BB',
            'image_url' => null,
            'user_id' => 1 // Ici, on fait le lien avec ton compte "Ton Pseudo" !
        ]);
    }
}
