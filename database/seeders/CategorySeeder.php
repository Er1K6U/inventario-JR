<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Baterías',
            'Controladores',
            'Frenos',
            'Llantas',
            'Luces',
            'Motor',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['name' => $name],
                [
                    'description' => 'Categoría inicial',
                    'active' => true,
                ]
            );
        }
    }
}