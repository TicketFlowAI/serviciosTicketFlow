<?php

namespace Database\Seeders;

use App\Models\ServiceContract;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ServiceContract::create([
        //     'company_id' => '2',
        //     'service_id' => '1',
        //     'service_term_id' => '1'
        // ]);
        // ServiceContract::create([
        //     'company_id' => '3',
        //     'service_id' => '1',
        //     'service_term_id' => '3'
        // ]);
        // ServiceContract::create([
        //     'company_id' => '20',
        //     'service_id' => '4',
        //     'service_term_id' => '2'
        // ]);
        // ServiceContract::create([
        //     'company_id' => '35',
        //     'service_id' => '5',
        //     'service_term_id' => '3'
        // ]);

        ServiceContract::create([
            'company_id' => '1',
            'service_id' => '1',
            'service_term_id' => '1'
        ]);
        ServiceContract::create([
            'company_id' => '1',
            'service_id' => '1',
            'service_term_id' => '3'
        ]);
        ServiceContract::create([
            'company_id' => '1',
            'service_id' => '4',
            'service_term_id' => '2'
        ]);
        ServiceContract::create([
            'company_id' => '1',
            'service_id' => '5',
            'service_term_id' => '3'
        ]);
    }
}
