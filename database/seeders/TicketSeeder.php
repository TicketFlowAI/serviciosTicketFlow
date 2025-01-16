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
        // Ticket::create([
        //     'service_contract_id' => 2,
        //     'title' => 'Como conectar una cuenta de correo',
        // ]);
        Ticket::create([
            'service_contract_id' => 3,
            'title' => 'Necesito espacio en mi hosting',
        ]);
    }
}
