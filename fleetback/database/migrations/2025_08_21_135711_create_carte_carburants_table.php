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
        Schema::create('carte_carburants', function (Blueprint $table) {
            $table->id();
            $table->string('n_carte');
            $table->decimal('plafond_caburant', 10, 3);
            $table->string('unite');
            $table->decimal('plafond_service', 10, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carte_carburants');
    }
};
