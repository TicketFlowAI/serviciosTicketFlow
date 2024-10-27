<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Mindsoft',
            'lastname' => 'Admin',
            'email' => 'info@mindsoft.biz',
            'password' => 'EstaEsUnaContraseña123*',
            'company_id' => 1
        ])->assignRole('super-admin');
        User::create([
            'name' => 'Mindsoft',
            'lastname' => 'technician',
            'email' => 'dennis.ocana@mindsoft.biz',
            'password' => 'EstaEsUnaCotraseña123*',
            'company_id' => 1
        ])->assignRole('technician');
        User::create([
            'name' => 'Jessica',
            'lastname' => 'Montero',
            'email' => 'jmontero@siegfried.com.ec',
            'password' => 'EstaEsUnaCotraseña123*',
            'company_id' => 1
        ])->assignRole('client');
    }
}
