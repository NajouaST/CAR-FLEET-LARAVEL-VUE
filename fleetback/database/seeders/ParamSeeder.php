<?php

namespace Database\Seeders;

use App\Models\FamilleVehicule;
use App\Models\Fournisseur;
use App\Models\Gamme;
use App\Models\Marque;
use App\Models\Modele;
use App\Models\TypeCarburant;
use App\Models\TypeCompteur;
use App\Models\Vehicule;
use Illuminate\Database\Seeder;

class ParamSeeder extends Seeder
{
    public function run(): void
    {
        // Familles
        $familles = [
            FamilleVehicule::create(['name' => 'Personnel', 'renouvelable' => 4]),
            FamilleVehicule::create(['name' => 'Utilitaire', 'renouvelable' => 1]),
            FamilleVehicule::create(['name' => 'Poids lourd', 'renouvelable' => 2]),
        ];

        // Fournisseurs
        $fournisseurs = [
            Fournisseur::create(['name' => 'Auto Distribution', 'email' => 'contact@autodistrib.com', 'tel' => '22112211', 'adresse' => 'Tunis']),
            Fournisseur::create(['name' => 'Car Import', 'email' => 'sales@carimport.com', 'tel' => '22334455', 'adresse' => 'Sfax']),
        ];

        // Carburants
        $diesel = TypeCarburant::create(['name' => 'Diesel']);
        $essence = TypeCarburant::create(['name' => 'Essence']);
        $ssp = TypeCarburant::create(['name' => 'SSP']);

        // Compteurs
        $km = TypeCompteur::create(['name' => 'KM']);
        $miles = TypeCompteur::create(['name' => 'Miles']);
    }

}
