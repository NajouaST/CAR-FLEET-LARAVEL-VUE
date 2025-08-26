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
            FamilleVehicule::firstOrCreate(['name' => 'Personnel', 'renouvelable' => 4]),
            FamilleVehicule::firstOrCreate(['name' => 'Utilitaire', 'renouvelable' => 1]),
            FamilleVehicule::firstOrCreate(['name' => 'Poids lourd', 'renouvelable' => 2]),
        ];

        // Fournisseurs
        $fournisseurs = [
            Fournisseur::firstOrCreate(['name' => 'Auto Distribution', 'email' => 'contact@autodistrib.com', 'tel' => '22112211', 'adresse' => 'Tunis']),
            Fournisseur::firstOrCreate(['name' => 'Car Import', 'email' => 'sales@carimport.com', 'tel' => '22334455', 'adresse' => 'Sfax']),
        ];

        // Carburants
        $diesel = TypeCarburant::firstOrCreate(['name' => 'Diesel']);
        $essence = TypeCarburant::firstOrCreate(['name' => 'Essence']);
        $ssp = TypeCarburant::firstOrCreate(['name' => 'SSP']);

        // Compteurs
        $km = TypeCompteur::firstOrCreate(['name' => 'KM']);
        $miles = TypeCompteur::firstOrCreate(['name' => 'Miles']);
    }

}
