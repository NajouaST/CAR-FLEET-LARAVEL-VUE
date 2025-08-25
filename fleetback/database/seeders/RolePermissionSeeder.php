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

            // ---------- VEHICULES ----------
            'vehicules access',
            'vehicules view (all)',
            'vehicules view (own)',
            'vehicules edit (all)',
            'vehicules edit (own)',
            'vehicules delete (all)',
            'vehicules delete (own)',
            'vehicules create',

            // ---------- PERSONNELS ----------
            'personnels access',
            'personnels view (all)',
            'personnels view (own)',
            'personnels edit (all)',
            'personnels edit (own)',
            'personnels delete (all)',
            'personnels delete (own)',
            'personnels create',

            // ---------- PARAMS ----------
            'rhparams access',
            // ---------- PARAMS ----------
            'parcparams access',
            // ---------- PARAMS ----------
            'params access',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = [
            'Admin',
            'Moderator',
            'Beneficiary',
        ];

        foreach ($roles as $name) {
            $admin = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            if($name == "Admin")
                $admin->syncPermissions(Permission::all());
        }
	}
}
