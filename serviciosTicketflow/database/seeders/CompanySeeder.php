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
        Company::create([
            'name' => 'AGLOMERADOS COTOPAXI S.A.',
            'idNumber' => '0590028665001',
            'contactEmail' => 'grp.contabilidad@cotopaxi.com.ec',
            'phone' => '023985200',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Lasso, Panamericana Norte Km. 21 desde Latacunga',
        ]);
        Company::create([
            'name' => 'AMALGAMA CIA LTDA',
            'idNumber' => '1792774780001',
            'contactEmail' => 'amalgamatoys@gmail.com',
            'phone' => '+5930998569871',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. de los Shyris n34-40 y Rep del Salvador',
        ]);
        Company::create([
            'name' => 'ASOCIACIÓN CULTURAL ACADEMIA COTOPAXI',
            'idNumber' => '1790105083001',
            'contactEmail' => 'msuarez@cotopaxi.k12.ec',
            'phone' => '023823270',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'De las Higuerrillas E16-102 y Alondras (Monteserrín)',
        ]);
    }
}
