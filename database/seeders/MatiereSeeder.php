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
                'code' => 'MATH',
                'coefficient' => 4,
                'description' => 'Mathématiques générales'
            ],
            [
                'nom' => 'Français',
                'code' => 'FRAN',
                'coefficient' => 4,
                'description' => 'Langue française et littérature'
            ],
            [
                'nom' => 'Histoire-Géographie',
                'code' => 'HIST',
                'coefficient' => 3,
                'description' => 'Histoire et géographie'
            ],
            [
                'nom' => 'Sciences',
                'code' => 'SCIE',
                'coefficient' => 3,
                'description' => 'Sciences de la vie et de la terre'
            ],
            [
                'nom' => 'Anglais',
                'code' => 'ANGL',
                'coefficient' => 2,
                'description' => 'Langue anglaise'
            ],
            [
                'nom' => 'Espagnol',
                'code' => 'ESP',
                'coefficient' => 2,
                'description' => 'Langue espagnole'
            ],
            [
                'nom' => 'Physique-Chimie',
                'code' => 'PHYS',
                'coefficient' => 3,
                'description' => 'Physique et chimie'
            ],
            [
                'nom' => 'Technologie',
                'code' => 'TECH',
                'coefficient' => 2,
                'description' => 'Technologie et informatique'
            ],
            [
                'nom' => 'Arts Plastiques',
                'code' => 'ARTS',
                'coefficient' => 1,
                'description' => 'Arts plastiques et visuels'
            ],
            [
                'nom' => 'Éducation Physique et Sportive',
                'code' => 'EPS',
                'coefficient' => 1,
                'description' => 'EPS et sports'
            ]
        ];

        foreach ($matieres as $matiere) {
            Matiere::create($matiere);
        }
    }
} 