<?php

namespace Database\Seeders;

use App\Models\Gamme;
use App\Models\Marque;
use App\Models\TypeCarburant;
use App\Models\TypeCompteur;
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
        // ---------- USERS ----------
        Permission::create(['name' => 'users access']);
        Permission::create(['name' => 'users view (all)']);
        Permission::create(['name' => 'users view (own)']);
        Permission::create(['name' => 'users create']);
        Permission::create(['name' => 'users edit (all)']);
        Permission::create(['name' => 'users edit (own)']);
        Permission::create(['name' => 'users delete (all)']);
        Permission::create(['name' => 'users delete (own)']);

        // ---------- ROLES ----------
        Permission::create(['name' => 'roles access']);
        Permission::create(['name' => 'roles view (all)']);
        Permission::create(['name' => 'roles create']);
        Permission::create(['name' => 'roles edit (all)']);
        Permission::create(['name' => 'roles delete (all)']);

        // ---------- MARQUES ----------
        Permission::create(['name' => 'params access']);

        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $editor = Role::create(['name' => 'moderator']);
        $viewer = Role::create(['name' => 'beneficiary']);

        $admin->syncPermissions(Permission::all());

        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'is_active' =>  true,
        ]);

        // Assign admin role
        $adminUser->assignRole('admin');

        TypeCarburant::create([ 'name' => 'Diesel']);
        TypeCarburant::create([ 'name' => 'SSP']);
        TypeCarburant::create([ 'name' => 'Gazole']);

        TypeCompteur::create([ 'name' => 'KM']);

        Gamme::create([ 'name' => 'SUV']);
        Gamme::create([ 'name' => 'Sedan']);
        Gamme::create([ 'name' => 'Hybrid']);

        Marque::create([ 'name' => 'BMW']);
        Marque::create([ 'name' => 'KIA']);
        Marque::create([ 'name' => 'FORD']);
    }
}

