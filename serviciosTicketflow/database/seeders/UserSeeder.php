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
            'email' => 'dennis.ocana@mindsoft.biz',
            'password' => 'EstaEsUnaCotraseña123*',
            'company' => 1
        ])->assignRole('super-admin');
    }
}
