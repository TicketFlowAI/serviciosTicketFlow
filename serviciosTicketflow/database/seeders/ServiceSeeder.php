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
            'category' => '1',
            'description' => 'Renovación Dominio .com - Anual',
            'price' => '60',
            'tax' => '1',
            'billedAnnually' => '1'
        ]);
        Service::create([
            'category' => '1',
            'description' => 'Renovación Dominio .ec/.com.ec - Anual',
            'price' => '60',
            'tax' => '1',
            'billedAnnually' => '1'
        ]);
        Service::create([
            'category' => '2',
            'description' => 'Plan Básico Hosting 5GB - Anual',
            'price' => '210',
            'tax' => '1',
            'billedAnnually' => '1'
        ]);
        Service::create([
            'category' => '2',
            'description' => 'Plan Básico Hosting 10GB - Anual',
            'price' => '270',
            'tax' => '1',
            'billedAnnually' => '1'
        ]);
        Service::create([
            'category' => '2',
            'description' => 'Plan 1 Hosting Dedicado',
            'price' => '100',
            'tax' => '1',
            'billedAnnually' => '0'
        ]);
    }
}
