<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Message::create([
            'ticket_id' => 1,
            'Content' => 'Estoy teniendo problemas con mis correos electrónicos. No se están enviando. ¿Pueden ayudarme a resolver esto?',
            'user_id' => 5
        ]);
        // Message::create([
        //     'ticket_id' => 2,
        //     'Content' => 'Necesito ayuda para conectar mi correo electrónico a Outlook. ¿Pueden proporcionar los pasos y los datos necesarios para la conexión?',
        //     'user_id' => 5
        // ]);
        Message::create([
            'ticket_id' => 2,
            'Content' => 'Estoy quedándome sin espacio en mi hosting. ¿Podrían aumentar mi espacio de alojamiento?',
            'user_id' => 5
        ]);
    }
}
