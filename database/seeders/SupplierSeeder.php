<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Electro Repuestos Central',
                'contact_name' => 'Carlos Gómez',
                'phone' => '3001112233',
                'email' => 'ventas@electrocentral.com',
                'address' => 'Calle 10 # 20-30',
                'active' => true,
            ],
            [
                'name' => 'Movilidad Verde SAS',
                'contact_name' => 'Laura Pérez',
                'phone' => '3004445566',
                'email' => 'contacto@movilidadverde.com',
                'address' => 'Carrera 15 # 40-12',
                'active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }
    }
}