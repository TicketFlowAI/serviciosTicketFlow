<?php

namespace Database\Seeders;

use App\Models\Email;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailTemplates = [
            'Expirando Mes' => 'emails/expiring_month.blade.php',
            'Expirando 2 semanas' => 'emails/expiring_two_weeks.blade.php',
            'Expirando 1 semana' => 'emails/expiring_week.blade.php',
            'Expirando 1 día' => 'emails/expiring_soon.blade.php',
        ];
        
        foreach ($emailTemplates as $templateName => $templatePath) {
            Email::create([
                'template_name' => $templateName,
                'body' => file_get_contents(resource_path("views/$templatePath")),
            ]);
        }
    }
}
