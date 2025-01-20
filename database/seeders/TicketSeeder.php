<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::create([
            'service_contract_id' => 1,
            'title' => 'Errores con los correos',
        ]);

        Ticket::create([
            'service_contract_id' => 2,
            'title' => 'Como conectar una cuenta de correo',
        ]);

        Ticket::create([
            'service_contract_id' => 3,
            'title' => 'Necesito espacio en mi hosting',
        ]);

        Ticket::create([
            'service_contract_id' => 4,
            'title' => 'Problemas con la base de datos',
        ]);

        Ticket::create([
            'service_contract_id' => 5,
            'title' => 'Error 404 en la página web',
        ]);

        Ticket::create([
            'service_contract_id' => 6,
            'title' => 'Problemas con el servidor',
        ]);

        Ticket::create([
            'service_contract_id' => 7,
            'title' => 'Necesito actualizar mi plan',
        ]);

        Ticket::create([
            'service_contract_id' => 8,
            'title' => 'Problemas con el certificado SSL',
        ]);

        Ticket::create([
            'service_contract_id' => 9,
            'title' => 'Necesito ayuda con la configuración de DNS',
        ]);

        Ticket::create([
            'service_contract_id' => 10,
            'title' => 'Problemas con el correo electrónico',
        ]);
    }
}
