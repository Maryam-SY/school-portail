<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enseignant_matiere', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enseignant_id'); // Référence à l'enseignant
            $table->unsignedBigInteger('matiere_id'); // Référence à la matière
            $table->unsignedBigInteger('classe_id'); // Référence à la classe
            $table->timestamps();

            // Clés étrangères
            $table->foreign('enseignant_id')->references('id')->on('enseignants')->onDelete('cascade');
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('cascade');
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['enseignant_id', 'matiere_id', 'classe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignant_matiere');
    }
};
