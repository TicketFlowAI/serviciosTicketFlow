<?php

namespace Database\Seeders;

use App\Models\ServiceTerm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceTerm::create([
            'term' => 'Mensual',
            'months' => '1'
        ]);
        ServiceTerm::create([
            'term' => 'Trimestral',
            'months' => '3'
        ]);
        ServiceTerm::create([
            'term' => 'Semestral',
            'months' => '6'
        ]);
        ServiceTerm::create([
            'term' => 'Anual',
            'months' => '12'
        ]);
    }
}
