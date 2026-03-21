<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['nom' => 'Relevé', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Entretien', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Réparation', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
