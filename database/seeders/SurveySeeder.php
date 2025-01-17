<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Survey::create([
                'ticket_id' => 1,
                'question_id' => $i,
                'user_id' => 1,
                'score' => rand(1, 5)
            ]);
        }
    }
}
