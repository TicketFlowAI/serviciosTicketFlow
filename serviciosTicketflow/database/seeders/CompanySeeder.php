<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'Mindsoft',
            'idNumber' => '1791888944001',
            'contactEmail' => 'info@mindsoft.biz',
            'phone' => '+593984258842',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Hernandez de Jirón y Av. América',
        ]);
    }
}
