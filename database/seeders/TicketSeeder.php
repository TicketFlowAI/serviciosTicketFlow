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
        $tickets = [
            ['service_contract_id' => 1, 'status'=> 2,'title' => 'Errores con los correos'],
            ['service_contract_id' => 2, 'status'=> 2, 'title' => 'Como conectar una cuenta de correo'],
            ['service_contract_id' => 3, 'status'=> 0, 'title' => 'Necesito espacio en mi hosting'],
            ['service_contract_id' => 4, 'status'=> 0,'title' => 'Problemas con la base de datos'],
            ['service_contract_id' => 1, 'status'=> 1, 'title' => 'Error 404 en la página web'],
            ['service_contract_id' => 2, 'status'=> 3, 'title' => 'Problemas con el servidor'],
            ['service_contract_id' => 3, 'status'=> 1, 'title' => 'Necesito actualizar mi plan'],
            ['service_contract_id' => 4, 'status'=> 3, 'title' => 'Problemas con el certificado SSL'],
            ['service_contract_id' => 1, 'status'=> 1, 'title' => 'Necesito ayuda con la configuración de DNS'],
            ['service_contract_id' => 2, 'status'=> 0, 'title' => 'Problemas con el correo electrónico'],
        ];

        foreach ($tickets as $ticket) {
            Ticket::create($ticket);
        }
    }
}
