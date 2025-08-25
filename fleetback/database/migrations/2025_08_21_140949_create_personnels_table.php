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
            $table->string('cin')->unique();

            // foreign keys
            $table->foreignId('carte_carburant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('societe_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('direction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('fonction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('departement_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('grade_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('centre_cout_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('tel');
            $table->string('superviseur')->nullable();
            $table->string('titre');
            $table->text('adresse');
            $table->enum('type', ['sédentaire', 'force de vente']);
            $table->enum('sexe', ['h', 'f']);

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
