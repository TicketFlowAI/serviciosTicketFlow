<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'category_id' => '1',
            'description' => 'Dominio .com',
            'price' => '60',
            'tax_id' => '1',
            'details' => 'Registro de dominio .com por un año',
        ]);
        Service::create([
            'category_id' => '1',
            'description' => 'Dominio .ec/.com.ec',
            'price' => '60',
            'tax_id' => '1',
            'details' => 'Registro de dominio .ec o .com.ec por un año',
        ]);
        Service::create([
            'category_id' => '2',
            'description' => 'Plan Básico Hosting 5GB',
            'price' => '210',
            'tax_id' => '1',
            'details' => 'Plan de hosting básico con 5GB de almacenamiento',
        ]);
        Service::create([
            'category_id' => '2',
            'description' => 'Plan Básico Hosting 10GB',
            'price' => '270',
            'tax_id' => '1',
            'details' => 'Plan de hosting básico con 10GB de almacenamiento',
        ]);
        Service::create([
            'category_id' => '2',
            'description' => 'Plan 1 Hosting Dedicado',
            'price' => '1200',
            'tax_id' => '1',
            'details' => 'Plan de hosting dedicado con recursos exclusivos',
        ]);
        Service::create([
            'category_id' => '2',
            'description' => 'Plan 2 Hosting Dedicado',
            'price' => '1800',
            'tax_id' => '1',
            'details' => 'Plan avanzado de hosting dedicado con más recursos',
        ]);

    }
}
