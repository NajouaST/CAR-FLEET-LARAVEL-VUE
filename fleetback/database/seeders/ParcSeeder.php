<?php

namespace Database\Seeders;

use App\Models\FamilleVehicule;
use App\Models\Fournisseur;
use App\Models\Gamme;
use App\Models\Marque;
use App\Models\Modele;
use App\Models\TypeCarburant;
use App\Models\TypeCompteur;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ParcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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

        // Gammes
        $suv = Gamme::firstOrCreate([ 'name' => 'SUV']);
        $sedan = Gamme::firstOrCreate([ 'name' => 'Sedan']);
        $hybride = Gamme::firstOrCreate([ 'name' => 'Hybrid']);

        // Marques
        $bmw = Marque::firstOrCreate([ 'name' => 'BMW']);
        $kia = Marque::firstOrCreate([ 'name' => 'KIA']);
        $ford = Marque::firstOrCreate([ 'name' => 'FORD']);
        $vw = Marque::firstOrCreate([ 'name' => 'VW']);

        // Modèles
        $modele1 = Modele::firstOrCreate([
            'name' => 'X5',
            'puissance_cv' => 300,
            'puissance_din' => 295,
            'cylindre' => 6,
            'poids_vide' => 2000,
            'poids_tc' => 2500,
            'charge_utile' => 500,
            'consommation_min' => 7.5,
            'consommation_max' => 10,
            'consommation_moy' => 8.5,
            'marque_id' => $bmw->id,
            'gamme_id' => $suv->id,
            'type_compteur_id' => 1,
            'type_carburant_id' => 1,
        ]);

        $modele2 = Modele::firstOrCreate([
            'name' => 'Picanto',
            'puissance_cv' => 70,
            'puissance_din' => 68,
            'cylindre' => 3,
            'poids_vide' => 950,
            'poids_tc' => 1200,
            'charge_utile' => 250,
            'consommation_min' => 4.5,
            'consommation_max' => 6,
            'consommation_moy' => 5.2,
            'marque_id' => $kia->id,
            'gamme_id' => $sedan->id,
            'type_compteur_id' => 1,
            'type_carburant_id' => 1,
        ]);

        $modele3 = Modele::firstOrCreate([
            'name' => 'Focus',
            'puissance_cv' => 100,
            'puissance_din' => 95,
            'cylindre' => 4,
            'poids_vide' => 1300,
            'poids_tc' => 1700,
            'charge_utile' => 400,
            'consommation_min' => 5,
            'consommation_max' => 7,
            'consommation_moy' => 6,
            'marque_id' => $ford->id,
            'gamme_id' => $sedan->id,
            'type_compteur_id' => 1,
            'type_carburant_id' => 1,
        ]);

        $modele4 = Modele::firstOrCreate([
            'name' => 'Transporter',
            'puissance_cv' => 140,
            'puissance_din' => 135,
            'cylindre' => 4,
            'poids_vide' => 2500,
            'poids_tc' => 3500,
            'charge_utile' => 1000,
            'consommation_min' => 9,
            'consommation_max' => 12,
            'consommation_moy' => 10.5,
            'marque_id' => $vw->id,
            'gamme_id' => $suv->id,
            'type_compteur_id' => 1,
            'type_carburant_id' => 1,
        ]);

        $modele5 = Modele::firstOrCreate([
            'name' => 'i3',
            'puissance_cv' => 170,
            'puissance_din' => 168,
            'cylindre' => 0,
            'poids_vide' => 1200,
            'poids_tc' => 1400,
            'charge_utile' => 200,
            'consommation_min' => 0,
            'consommation_max' => 0,
            'consommation_moy' => 0,
            'marque_id' => $bmw->id,
            'gamme_id' => $hybride->id,
            'type_compteur_id' => 1,
            'type_carburant_id' => 1,
        ]);

        // Véhicules (4–5)
        // Véhicules
        $vehicule1 = Vehicule::firstOrCreate([
            'carte_grise' => 'cg_bmw_x5.pdf',
            'immatriculation' => '125-TN-456',
            'chassis' => 'CHASSISBMW125',
            'dmc' => now()->subYears(2),
            'couleur' => 'Noir',
            'categorie' => '4x4',
            'situation' => 'en_exploitation',   // ✅
            'statut' => 'affectee',             // ✅
            'formule_achat' => 'fonds propres',
            'date' => now()->subYears(2),
            'valeur' => 120000,
            'loyer' => null,
            'date_garantie' => now()->addYear(),
            'km_garantie' => 100000,
            'modele_id' => $modele1->id,
            'famille_vehicule_id' => 1,
            'fournisseur_id' => 1,
        ]);

        $vehicule2 = Vehicule::firstOrCreate([
            'carte_grise' => 'cg_kia_picanto.pdf',
            'immatriculation' => '784-TN-321',
            'chassis' => 'CHASSISKIA784',
            'dmc' => now()->subYear(),
            'couleur' => 'Rouge',
            'categorie' => 'Citadine',
            'situation' => 'en_exploitation',   // ✅
            'statut' => 'libre',                // ✅
            'formule_achat' => 'leasing',
            'date' => now()->subYear(),
            'valeur' => 35000,
            'loyer' => 700,
            'date_garantie' => now()->addYears(2),
            'km_garantie' => 60000,
            'modele_id' => $modele2->id,
            'famille_vehicule_id' => 1,
            'fournisseur_id' => 1,
        ]);

        $vehicule3 = Vehicule::firstOrCreate([
            'carte_grise' => 'cg_ford_focus.pdf',
            'immatriculation' => '454-TN-654',
            'chassis' => 'CHASSISFORD454',
            'dmc' => now()->subYears(3),
            'couleur' => 'Bleu',
            'categorie' => 'Compacte',
            'situation' => 'en_reparation',     // ✅
            'statut' => 'hors_service',         // ✅
            'formule_achat' => 'fonds propres',
            'date' => now()->subYears(3),
            'valeur' => 70000,
            'loyer' => null,
            'date_garantie' => now()->subMonths(6),
            'km_garantie' => 80000,
            'modele_id' => $modele3->id,
            'famille_vehicule_id' => 1,
            'fournisseur_id' => 1,
        ]);

        $vehicule4 = Vehicule::firstOrCreate([
            'carte_grise' => 'cg_vw_transporter.pdf',
            'immatriculation' => '943-TN-852',
            'chassis' => 'CHASSISVW943',
            'dmc' => now()->subYears(4),
            'couleur' => 'Blanc',
            'categorie' => 'Utilitaire',
            'situation' => 'en_exploitation',   // ✅
            'statut' => 'affectee',             // ✅
            'formule_achat' => 'fonds propres',
            'date' => now()->subYears(4),
            'valeur' => 90000,
            'loyer' => null,
            'date_garantie' => now()->subYear(),
            'km_garantie' => 120000,
            'modele_id' => $modele4->id,
            'famille_vehicule_id' => 2,
            'fournisseur_id' => 2,
        ]);

        $vehicule5 = Vehicule::firstOrCreate([
            'carte_grise' => 'cg_bmw_i3.pdf',
            'immatriculation' => '149-TN-753',
            'chassis' => 'CHASSISBMW149',
            'dmc' => now()->subMonths(10),
            'couleur' => 'Gris',
            'categorie' => 'Hybride',
            'situation' => 'en_exploitation',   // ✅
            'statut' => 'libre',                // ✅
            'formule_achat' => 'leasing',
            'date' => now()->subMonths(10),
            'valeur' => 150000,
            'loyer' => 1500,
            'date_garantie' => now()->addYears(3),
            'km_garantie' => 80000,
            'modele_id' => $modele5->id,
            'famille_vehicule_id' => 1,
            'fournisseur_id' => 2,
        ]);
    }
}
