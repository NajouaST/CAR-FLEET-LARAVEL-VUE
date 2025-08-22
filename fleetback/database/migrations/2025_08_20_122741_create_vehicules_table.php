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
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();

            $table->string('carte_grise')->nullable();
            $table->string('carte_grise_front')->nullable();
            $table->string('carte_grise_back')->nullable();

            // Identité
            $table->string('immatriculation')->unique();
            $table->string('chassis')->unique();
            $table->date('dmc')->nullable(); // Date de mise en circulation

            // Caractéristiques
            $table->string('couleur')->nullable();
            $table->string('categorie')->nullable();

            // Situation
            $table->enum('situation', [
                'en_exploitation',
                'en_reparation',
                'accidentee',
                'arretee',
                'litige',
                'epave',
                'vendue'
            ])->default('en_exploitation');

            $table->enum('statut', [
                'libre',
                'affectee',
                'hors_service',
                'vendue'
            ])->default('libre');

            // Achat
            $table->enum('formule_achat', [
                'fonds propres',
                'leasing',
                'lld'
            ])->default('fonds propres');

            $table->date('date')->nullable(); // Date d'achat
            $table->decimal('valeur', 12, 3)->nullable();
            $table->decimal('loyer', 12, 3)->nullable();
            $table->date('date_garantie')->nullable();
            $table->decimal('km_garantie', 12, 3)->nullable();

            // Cession
            $table->date('date_cession')->nullable();
            $table->decimal('valeur_cession', 12, 3)->nullable();

            $table->unsignedBigInteger('assigned_to')->nullable();

            // Clés étrangères
            $table->foreignId('modele_id')->constrained()->cascadeOnDelete();
            $table->foreignId('famille_vehicule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fournisseur_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
