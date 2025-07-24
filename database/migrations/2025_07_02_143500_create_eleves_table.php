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
            $table->unsignedBigInteger('user_id'); // Clé étrangère vers users (élève)
            $table->unsignedBigInteger('parent_user_id')->nullable(); // Clé étrangère vers users (parent)
            $table->string('nom'); 
            $table->string('prenom'); 
            $table->date('date_naissance'); 
            $table->string('email')->unique(); 
            $table->string('telephone')->nullable(); 
            $table->string('adresse')->nullable(); 
            $table->unsignedBigInteger('classe_id'); // Référence à la classe
            $table->string('identifiant')->unique(); 
            $table->string('document_justificatif')->nullable();
            $table->timestamps();

            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_user_id')->references('id')->on('users')->onDelete('set null');
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
