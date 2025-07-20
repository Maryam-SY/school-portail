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
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); 
            $table->string('prenom'); 
            $table->date('date_naissance'); 
            $table->string('email')->unique(); 
            $table->string('telephone')->nullable(); 
            $table->string('adresse')->nullable(); 
            $table->unsignedBigInteger('classe_id'); // Référence à la classe n p neg
            $table->string('identifiant')->unique(); 
            $table->string('document_justificatif')->nullable();
            $table->timestamps();

            // Clé étrangère vers la table classes
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};
