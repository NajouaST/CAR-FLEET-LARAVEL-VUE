<?php

namespace Database\Seeders;

use App\Models\Gamme;
use App\Models\Marque;
use App\Models\TypeCarburant;
use App\Models\TypeCompteur;
use App\Models\Region;
use App\Models\Zone;
use App\Models\Site;
use App\Models\Societe;
use App\Models\Direction;
use App\Models\Departement;
use App\Models\Fonction;
use App\Models\Grade;
use App\Models\Division;
use App\Models\CentreCout;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$permissions = [
			// ---------- USERS ----------
			'users access',
			'users view (all)',
			'users view (own)',
			'users create',
			'users edit (all)',
			'users edit (own)',
			'users delete (all)',
			'users delete (own)',

			// ---------- ROLES ----------
			'roles access',
			'roles view (all)',
			'roles create',
			'roles edit (all)',
			'roles delete (all)',

			// ---------- PARAMS ----------
			'params access',
		];

		foreach ($permissions as $name) {
			Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
		}

		// Create roles
		$admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
		$editor = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
		$viewer = Role::firstOrCreate(['name' => 'beneficiary', 'guard_name' => 'web']);

		$admin->syncPermissions(Permission::all());

		// Create admin user
		$adminUser = User::firstOrCreate(
			['email' => 'admin@example.com'],
			[
				'name' => 'Admin User',
				'password' => Hash::make('12345678'),
				'is_active' => true,
			]
		);

		// Assign admin role
		if (!$adminUser->hasRole('admin')) {
			$adminUser->assignRole('admin');
		}

		// Params
		TypeCarburant::firstOrCreate(['name' => 'Diesel']);
		TypeCarburant::firstOrCreate(['name' => 'SSP']);
		TypeCarburant::firstOrCreate(['name' => 'Gazole']);

		TypeCompteur::firstOrCreate(['name' => 'KM']);

		// region seeder
		$regions = [
            ['nom' => 'Region Casablanca-Settat'],
            ['nom' => 'Region Rabat-Salé-Kénitra'],
            ['nom' => 'Region Tanger-Tétouan-Al Hoceïma'],
            ['nom' => 'Region Fès-Meknès'],
            ['nom' => 'Region Marrakech-Safi'],
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate($region);
        }

		// zone seeder
		$zones = [
            ['nom' => 'Zone Casablanca-Settat'],
            ['nom' => 'Zone Rabat-Salé-Kénitra'],
            ['nom' => 'Zone Tanger-Tétouan-Al Hoceïma'],
            ['nom' => 'Zone Fès-Meknès'],
            ['nom' => 'Zone Marrakech-Safi'],
        ];
		
		foreach ($zones as $zone) {
			Zone::firstOrCreate($zone);
		}

		// site seeder
		$sites = [
            ['nom' => 'Site Casablanca-Settat'],
            ['nom' => 'Site Rabat-Salé-Kénitra'],
            ['nom' => 'Site Tanger-Tétouan-Al Hoceïma'],
            ['nom' => 'Site Fès-Meknès'],
            ['nom' => 'Site Marrakech-Safi'],
        ];
		
		foreach ($sites as $site) {
			Site::firstOrCreate($site);
		}

		// societe seeder
		$societes = [
			['nom' => 'Société Casablanca-Settat'],
			['nom' => 'Société Rabat-Salé-Kénitra'],
			['nom' => 'Société Tanger-Tétouan-Al Hoceïma'],
			['nom' => 'Société Fès-Meknès'],
			['nom' => 'Société Marrakech-Safi'],
		];
		foreach ($societes as $societe) {
			Societe::firstOrCreate($societe);
		}

		// direction seeder
		$directions = [
			['nom' => 'Direction Casablanca-Settat'],
			['nom' => 'Direction Rabat-Salé-Kénitra'],
		];
		
		foreach ($directions as $direction) {
			Direction::firstOrCreate($direction);
		}

		// departement seeder
		$departements = [
			['nom' => 'Departement Casablanca-Settat'],
			['nom' => 'Departement Rabat-Salé-Kénitra'],
		];
		
		foreach ($departements as $departement) {
			Departement::firstOrCreate($departement);
		}

		// fonction seeder
		$fonctions = [
			['nom' => 'Fonction Casablanca-Settat'],
			['nom' => 'Fonction Rabat-Salé-Kénitra'],
		];
		
		foreach ($fonctions as $fonction) {
			Fonction::firstOrCreate($fonction);
		}	

		// grade seeder
		$grades = [
			['nom' => 'Grade Casablanca-Settat'],
			['nom' => 'Grade Rabat-Salé-Kénitra'],
		];
		
		foreach ($grades as $grade) {
			Grade::firstOrCreate($grade);
		}

		// division seeder
		$divisions = [
			['nom' => 'Division Casablanca-Settat'],
			['nom' => 'Division Rabat-Salé-Kénitra'],
		];
		
		foreach ($divisions as $division) {
			Division::firstOrCreate($division);
		}
		// centre cout seeder
		$centreCouts = [
			['nom' => 'Centre Cout Casablanca-Settat'],
			['nom' => 'Centre Cout Rabat-Salé-Kénitra'],
		];
		
		foreach ($centreCouts as $centreCout) {
			CentreCout::firstOrCreate($centreCout);
		}

		// personnels seeder

		Personnel::firstOrCreate(['email' => 'john.doe@example.com'], [
			'matriculation' => 'EMP001',
			'nom' => 'John Doe',
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
			'tjrs_actif' => 'oui',
			'num_carte_carb' => 123456,
			'num_permis' => 'PERMIS001',
			'delivre_le' => '2020-01-15',
			'fin_validite' => '2030-01-15',
		]);
		
		Personnel::firstOrCreate(['email' => 'jane.smith@example.com'], [
			'matriculation' => 'EMP002',
			'nom' => 'Jane Smith',
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
			'tel' => '+1234567891',
			'superviseur' => 'John Doe',
			'titre' => 'Manager',
			'adresse' => '456 Oak Avenue, City',
			'type' => 'sédentaire',
			'sexe' => 'f',
			'tjrs_actif' => 'oui',
			'num_carte_carb' => 789012,
			'num_permis' => 'PERMIS002',
			'delivre_le' => '2019-03-20',
			'fin_validite' => '2029-03-20',
		]);

		Gamme::firstOrCreate(['name' => 'SUV']);
		Gamme::firstOrCreate(['name' => 'Sedan']);
		Gamme::firstOrCreate(['name' => 'Hybrid']);

		Marque::firstOrCreate(['name' => 'BMW']);
		Marque::firstOrCreate(['name' => 'KIA']);
		Marque::firstOrCreate(['name' => 'FORD']);
	}
}