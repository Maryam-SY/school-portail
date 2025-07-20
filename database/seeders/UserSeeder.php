<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@portail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        User::firstOrCreate(
            ['email' => 'enseignant@portail.com'],
            [
                'name' => 'Enseignant',
                'password' => Hash::make('password'),
                'role' => 'enseignant'
            ]
        );

        User::firstOrCreate(
            ['email' => 'eleve@portail.com'],
            [
                'name' => 'Élève',
                'password' => Hash::make('password'),
                'role' => 'eleve'
            ]
        );
    }
} 