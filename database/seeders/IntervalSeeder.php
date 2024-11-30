<?php

namespace Database\Seeders;

use App\Models\Interval;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IntervalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $intervals = [
            [
                'days' => 30,
                'type' => 'Mes',
                'email_id' => '1',
            ],
            [
                'days' => 15,
                'type' => '2 Semanas',
                'email_id' => '2',
            ],
            [
                'days' => 30,
                'type' => '1 Semana',
                'email_id' => '3',
            ],
            [
                'days' => 30,
                'type' => '1 Día',
                'email_id' => '4',
            ],

        ];

        foreach ($intervals as $interval) {
            Interval::create($interval);
        }
    }
}
