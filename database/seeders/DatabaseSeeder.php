<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClasseSeeder::class,
            MatiereSeeder::class,
        ]);

        // Lier automatiquement chaque enseignant à son user (par email)
        \App\Models\Enseignant::query()->each(function($enseignant) {
            $user = \App\Models\User::where('email', $enseignant->email)->first();
            if ($user) {
                $enseignant->user_id = $user->id;
                $enseignant->save();
            }
        });
    }
}
