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
            CompanySeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TaxSeeder::class,
            ServiceSeeder::class,
            ServiceTermSeeder::class,
            ServiceContractSeeder::class,
            ServiceContractSeeder::class,
            TicketSeeder::class,
            MessageSeeder::class,
            EmailSeeder::class,
            IntervalSeeder::class,
        ]);
    }
}
