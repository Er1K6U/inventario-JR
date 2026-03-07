<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $userRole = Role::firstOrCreate(['name' => 'Usuario']);
        $sellerRole = Role::firstOrCreate(['name' => 'Vendedor']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@inventariojr.com'],
            [
                'name' => 'Administrador General',
                'password' => bcrypt('Admin12345!'),
            ]
        );

        if (!$admin->hasRole($adminRole->name)) {
            $admin->assignRole($adminRole);
        }
    }
}