<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);
        $this->call([
            CompanySeeder::class,
        ]);
        $this->call([
            UserSeeder::class,
        ]);
        $this->call([
            CategorySeeder::class,
        ]);
        $this->call([
            TaxSeeder::class,
        ]);
        $this->call([
            ServiceSeeder::class,
        ]);
        $this->call([
            ServiceTermSeeder::class,
        ]);
        $this->call([
            ServiceContractSeeder::class,
        ]);
    }
}
