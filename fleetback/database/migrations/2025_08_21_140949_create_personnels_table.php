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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('matriculation')->unique();
            $table->string('nom');
            $table->string('cin')->unique();

            // foreign keys
            $table->foreignId('societe_id')->constrained('societes');
            $table->foreignId('direction_id')->constrained('directions');
            $table->foreignId('fonction_id')->constrained('fonctions');
            $table->foreignId('region_id')->constrained('regions');
            $table->foreignId('zone_id')->constrained('zones');
            $table->foreignId('site_id')->constrained('sites');
            $table->foreignId('departement_id')->constrained('departements');
            $table->foreignId('grade_id')->constrained('grades');
            $table->foreignId('division_id')->constrained('divisions');
            $table->foreignId('centre_cout_id')->constrained('centre_couts');

            $table->string('tel');
            $table->string('superviseur')->nullable();
            $table->string('email')->unique();
            $table->string('titre');
            $table->text('adresse');
            $table->enum('type', ['sédentaire', 'force de vente']);
            $table->enum('sexe', ['h', 'f']);
            $table->enum('tjrs_actif', ['oui', 'non'])->default('oui');
            $table->bigInteger('num_carte_carb')->nullable();

            // permis
            $table->string('num_permis');
            $table->date('delivre_le');
            $table->date('fin_validite');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
