<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SurveyQuestion;

class SurveyQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SurveyQuestion::create(['question' => '¿Cómo calificaría la satisfacción con el servicio de soporte recibido?']);
        SurveyQuestion::create(['question' => '¿Cómo calificaría la rapidez de respuesta a su solicitud?']);
        SurveyQuestion::create(['question' => '¿Cómo se siente respecto al servicio que solicitó?']);
        SurveyQuestion::create(['question' => '¿Cómo calificaría la amabilidad del personal de soporte?']);
        SurveyQuestion::create(['question' => '¿Cómo calificaría la claridad de la información proporcionada?']);
    }
}
