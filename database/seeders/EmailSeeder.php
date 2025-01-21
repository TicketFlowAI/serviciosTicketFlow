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
            [
                'template_name' => 'Expirando Mes',
                'subject' => 'Notificación temprana - Su {service} caduca en {days} días',
                'body' => file_get_contents(resource_path('views/emails/expiring_month.blade.php')),
            ],
            [
                'template_name' => 'Expirando 2 semanas',
                'subject' => 'Su {service} caduca en {days} días',
                'body' => file_get_contents(resource_path('views/emails/expiring_two_weeks.blade.php')),
            ],
            [
                'template_name' => 'Expirando 1 semana',
                'subject' => 'Su {service} caduca en {days} días',
                'body' => file_get_contents(resource_path('views/emails/expiring_week.blade.php')),
            ],
            [
                'template_name' => 'Expirando 1 día',
                'subject' => 'Urgente - Su {service} caduca en {days} días',
                'body' => file_get_contents(resource_path('views/emails/expiring_soon.blade.php')),
            ],
            [
                'template_name' => 'Solicitud de cancelación de servicios',
                'subject' => '{company} ha solicitado la cancelación de su {service}',
                'body' => file_get_contents(resource_path('views/emails/serviceCancellation.blade.php')),
            ],
            [
                'template_name' => 'Solicitud de servicios',
                'subject' => '{company} ha solicitado un nuevo servicio',
                'body' => file_get_contents(resource_path('views/emails/serviceRequest.blade.php')),
            ],
        ];

        foreach ($emailTemplates as $template) {
            Email::create($template);
        }
    }
}
