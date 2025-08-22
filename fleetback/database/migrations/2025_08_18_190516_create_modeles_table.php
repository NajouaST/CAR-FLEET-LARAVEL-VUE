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
        Schema::create('modeles', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->decimal('puissance_cv')->nullable();
            $table->decimal('puissance_din')->nullable();

            $table->integer('places')->nullable();

            $table->decimal('poids_vide')->nullable();
            $table->decimal('poids_tc')->nullable();
            $table->decimal('charge_utile')->nullable();

            $table->decimal('cylindre')->nullable();

            $table->decimal('consommation_min')->nullable();
            $table->decimal('consommation_max')->nullable();
            $table->decimal('consommation_moy')->nullable();

            $table->foreignId('marque_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gamme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('type_compteur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('type_carburant_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modeles');
    }
};
