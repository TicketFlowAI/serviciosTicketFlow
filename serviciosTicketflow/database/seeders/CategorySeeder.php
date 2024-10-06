<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'category' => 'Dominios'
        ]);
        Category::create([
            'category' => 'Hosting'
        ]);
        Category::create([
            'category' => 'Licencias'
        ]);
        Category::create([
            'category' => 'Mantenimiento/Soporte continuo'
        ]);
    }
}
