<?php

namespace Database\Seeders;

use App\Models\CentreCout;
use App\Models\Departement;
use App\Models\Direction;
use App\Models\Division;
use App\Models\Fonction;
use App\Models\Grade;
use App\Models\Personnel;
use App\Models\Region;
use App\Models\Site;
use App\Models\Societe;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class PersonnelSeeder extends Seeder
{
    public function run(): void
    {
        // region seeder
        $regions = [
            ['nom' => 'Grand Tunis'],
            ['nom' => 'Nord-Est'],
            ['nom' => 'Nord-Ouest'],
            ['nom' => 'Centre-Est'],
            ['nom' => 'Centre-Ouest'],
            ['nom' => 'Sud-Est'],
            ['nom' => 'Sud-Ouest'],
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate($region);
        }

        // zone seeder
        $zones = [
            ['nom' => 'Tunis'],
            ['nom' => 'Sfax'],
            ['nom' => 'Sousse'],
            ['nom' => 'Bizerte'],
            ['nom' => 'Gabès'],
        ];

        foreach ($zones as $zone) {
            Zone::firstOrCreate($zone);
        }

        // site seeder
        $sites = [
            ['nom' => 'Tunis'],
            ['nom' => 'Sfax'],
            ['nom' => 'Sousse'],
            ['nom' => 'Bizerte'],
            ['nom' => 'Gabès'],
        ];

        foreach ($sites as $site) {
            Site::firstOrCreate($site);
        }

        // societe seeder
        $societes = [
            ['nom' => 'Nationale de Transport'],
            ['nom' => 'Industrielle du Sahel'],
            ['nom' => 'Commerciale de Tunis'],
            ['nom' => 'Pétrolière du Sud'],
            ['nom' => 'Agroalimentaire du Centre'],
        ];

        foreach ($societes as $societe) {
            Societe::firstOrCreate($societe);
        }

        // direction seeder
        $directions = [
            ['nom' => 'Générale Tunis'],
            ['nom' => 'Régionale Sfax'],
        ];

        foreach ($directions as $direction) {
            Direction::firstOrCreate($direction);
        }

        // departement seeder
        $departements = [
            ['nom' => 'Informatique'],
            ['nom' => 'Ressources Humaines'],
        ];

        foreach ($departements as $departement) {
            Departement::firstOrCreate($departement);
        }

        // fonction seeder
        $fonctions = [
            ['nom' => 'Chef de Projet'],
            ['nom' => 'Technicien Supérieur'],
            ['nom' => 'Analyste Financier'],
        ];

        foreach ($fonctions as $fonction) {
            Fonction::firstOrCreate($fonction);
        }

        // grade seeder
        $grades = [
            ['nom' => 'Agent'],
            ['nom' => 'Cadre'],
            ['nom' => 'Cadre Supérieur'],
        ];

        foreach ($grades as $grade) {
            Grade::firstOrCreate($grade);
        }

        // division seeder
        $divisions = [
            ['nom' => 'Commerciale'],
            ['nom' => 'Technique'],
            ['nom' => 'Logistique'],
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate($division);
        }

        // centre cout seeder
        $centreCouts = [
            ['nom' => 'Tunis'],
            ['nom' => 'Sfax'],
            ['nom' => 'Sousse'],
        ];

        foreach ($centreCouts as $centreCout) {
            CentreCout::firstOrCreate($centreCout);
        }


        // personnels seeder

        Personnel::firstOrCreate( [
            'matriculation' => 'EMP001',
            'cin' => 'AB123456',
            'societe_id' => 1,
            'direction_id' => 1,
            'fonction_id' => 1,
            'region_id' => 1,
            'zone_id' => 1,
            'site_id' => 1,
            'departement_id' => 1,
            'grade_id' => 1,
            'division_id' => 1,
            'centre_cout_id' => 1,
            'tel' => '+1234567890',
            'superviseur' => null,
            'titre' => 'Driver',
            'adresse' => '123 Main Street, City',
            'type' => 'force de vente',
            'sexe' => 'h',
            'num_permis' => 'PERMIS001',
            'delivre_le' => '2020-01-15',
            'fin_validite' => '2030-01-15',
        ]);

        Personnel::firstOrCreate( [
            'matriculation' => 'EMP002',
            'cin' => 'CD789012',
            'societe_id' => 1,
            'direction_id' => 1,
            'fonction_id' => 1,
            'region_id' => 1,
            'zone_id' => 1,
            'site_id' => 1,
            'departement_id' => 1,
            'grade_id' => 1,
            'division_id' => 1,
            'centre_cout_id' => 1,
            'user_id' => 1,
            'tel' => '+1234567891',
            'superviseur' => 'John Doe',
            'titre' => 'Manager',
            'adresse' => '456 Oak Avenue, City',
            'type' => 'sédentaire',
            'sexe' => 'f',
            'num_permis' => 'PERMIS002',
            'delivre_le' => '2019-03-20',
            'fin_validite' => '2029-03-20',
        ]);
    }
}
