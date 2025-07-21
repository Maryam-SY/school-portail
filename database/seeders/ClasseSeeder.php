<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;

class ClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            [
                'nom' => '6ème A',
                'niveau' => '6ème',
                'capacite' => 30,
                'annee_scolaire' => '2024-2025'
            ],
            [
                'nom' => '6ème B',
                'niveau' => '6ème',
                'capacite' => 28,
                'annee_scolaire' => '2024-2025'
            ],
            [
                'nom' => '5ème A',
                'niveau' => '5ème',
                'capacite' => 32,
                'annee_scolaire' => '2024-2025'
            ],
            [
                'nom' => '5ème B',
                'niveau' => '5ème',
                'capacite' => 30,
                'annee_scolaire' => '2024-2025'
            ],
            [
                'nom' => '4ème A',
                'niveau' => '4ème',
                'capacite' => 29,
                'annee_scolaire' => '2024-2025'
            ],
            [
                'nom' => '3ème A',
                'niveau' => '3ème',
                'capacite' => 31,
                'annee_scolaire' => '2024-2025'
            ]
        ];

        foreach ($classes as $classe) {
            Classe::create($classe);
        }
    }
} 