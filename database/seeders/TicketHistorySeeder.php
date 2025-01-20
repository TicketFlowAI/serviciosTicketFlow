<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TicketHistory;

class TicketHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TicketHistory::create([
            'ticket_id' => 1,
            'user_id' => 5,
            'action' => 'Created ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 1,
            'user_id' => 2,
            'action' => 'Updated ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 2,
            'user_id' => 5,
            'action' => 'Created ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 2,
            'user_id' => 1,
            'action' => 'Closed ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 3,
            'user_id' => 5,
            'action' => 'Created ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 3,
            'user_id' => 3,
            'action' => 'Updated ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 4,
            'user_id' => 5,
            'action' => 'Created ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 4,
            'user_id' => 4,
            'action' => 'Updated ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 5,
            'user_id' => 5,
            'action' => 'Created ticket'
        ]);

        TicketHistory::create([
            'ticket_id' => 5,
            'user_id' => 2,
            'action' => 'Closed ticket'
        ]);
    }
}
