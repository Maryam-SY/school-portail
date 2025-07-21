<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatiereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matieres = [
            [
                'nom' => 'Mathématiques',
                'coefficient' => 4,
                'description' => 'Mathématiques générales'
            ],
            [
                'nom' => 'Français',
                'coefficient' => 4,
                'description' => 'Langue française et littérature'
            ],
            [
                'nom' => 'Histoire-Géographie',
                'coefficient' => 3,
                'description' => 'Histoire et géographie'
            ],
            [
                'nom' => 'Sciences',
                'coefficient' => 3,
                'description' => 'Sciences de la vie et de la terre'
            ],
            [
                'nom' => 'Anglais',
                'coefficient' => 2,
                'description' => 'Langue anglaise'
            ],
            [
                'nom' => 'Espagnol',
                'coefficient' => 2,
                'description' => 'Langue espagnole'
            ],
            [
                'nom' => 'Physique-Chimie',
                'coefficient' => 3,
                'description' => 'Physique et chimie'
            ],
            [
                'nom' => 'Technologie',
                'coefficient' => 2,
                'description' => 'Technologie et informatique'
            ],
            [
                'nom' => 'Arts Plastiques',
                'coefficient' => 1,
                'description' => 'Arts plastiques et visuels'
            ],
            [
                'nom' => 'Éducation Physique et Sportive',
                'coefficient' => 1,
                'description' => 'EPS et sports'
            ]
        ];

        foreach ($matieres as $matiere) {
            Matiere::create($matiere);
        }
    }
} 