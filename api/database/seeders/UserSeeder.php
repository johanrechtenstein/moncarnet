<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
        'pseudo' => 'Ton Pseudo',
        'email' => 'test@example.com',
        'password' => Hash::make('password'), // Toujours hasher le mot de passe !
        'avatar_url' => '...'
    ]);
    }
}
