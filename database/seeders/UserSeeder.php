<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private $defaultPassword;

    public function __construct()
    {
        $this->defaultPassword = env('DEFAULT_USER_PASSWORD');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Mindsoft',
            'lastname' => 'Admin',
            'email' => 'notificaciones@mindsoft.biz',
            'password' => Hash::make($this->defaultPassword),
            'company_id' => 1
        ])->assignRole('super-admin');
        User::create([
            'name' => 'Technician',
            'lastname' => 'Level1',
            'email' => 'tecnico1@mindsoft.biz',
            'password' => Hash::make($this->defaultPassword),
            'company_id' => 1
        ])->assignRole('technician','1');
        User::create([
            'name' => 'Technician',
            'lastname' => 'Level2',
            'email' => 'tecnico2@mindsoft.biz',
            'password' => Hash::make($this->defaultPassword),
            'company_id' => 1
        ])->assignRole('technician','2');
        User::create([
            'name' => 'Technician',
            'lastname' => 'Level3',
            'email' => 'tecnico3@mindsoft.biz',
            'password' => Hash::make($this->defaultPassword),
            'company_id' => 1
        ])->assignRole('technician','3');
        User::create([
            'name' => 'Jessica',
            'lastname' => 'Montero',
            'email' => 'jmontero@siegfried.com.ec',
            'password' => Hash::make($this->defaultPassword),
            'company_id' => 2
        ])->assignRole('client');
        User::create([
            'name' => 'Asistente',
            'lastname' => 'Digital',
            'email' => 'noreply@mindsoft.biz',
            'password' => Hash::make($this->defaultPassword),
            'company_id' => 1
        ])->assignRole('technician');
    }
}
