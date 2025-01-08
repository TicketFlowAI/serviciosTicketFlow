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
            'name' => 'Technician',
            'lastname' => 'Level1',
            'email' => 'tecnico1@mindsoft.biz',
            'password' => 'EstaEsUnaContraseña123*',
            'company_id' => 1
        ])->assignRole('technician','1');
        User::create([
            'name' => 'Technician',
            'lastname' => 'Level2',
            'email' => 'tecnico2@mindsoft.biz',
            'password' => 'EstaEsUnaContraseña123*',
            'company_id' => 1
        ])->assignRole('technician','2');
        User::create([
            'name' => 'Technician',
            'lastname' => 'Level3',
            'email' => 'tecnico3@mindsoft.biz',
            'password' => 'EstaEsUnaContraseña123*',
            'company_id' => 1
        ])->assignRole('technician','3');
        User::create([
            'name' => 'Jessica',
            'lastname' => 'Montero',
            'email' => 'jmontero@siegfried.com.ec',
            'password' => 'EstaEsUnaContraseña123*',
            'company_id' => 2
        ])->assignRole('client');
    }
}
