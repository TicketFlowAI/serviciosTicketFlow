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

        Message::create([
            'ticket_id' => 2,
            'Content' => 'Necesito ayuda para conectar mi correo electrónico a Outlook. ¿Pueden proporcionar los pasos y los datos necesarios para la conexión?',
            'user_id' => 5
        ]);

        Message::create([
            'ticket_id' => 3,
            'Content' => 'Estoy quedándome sin espacio en mi hosting. ¿Podrían aumentar mi espacio de alojamiento?',
            'user_id' => 5
        ]);

        Message::create([
            'ticket_id' => 4,
            'Content' => 'Estoy teniendo problemas con la base de datos. ¿Pueden ayudarme a resolver esto?',
            'user_id' => 5
        ]);

        Message::create([
            'ticket_id' => 5,
            'Content' => 'Estoy recibiendo un error 404 en mi página web. ¿Pueden ayudarme a resolver esto?',
            'user_id' => 5
        ]);

        Message::create([
            'ticket_id' => 6,
            'Content' => 'Estoy teniendo problemas con el servidor. ¿Pueden ayudarme a resolver esto?',
            'user_id' => 5
        ]);

        Message::create([
            'ticket_id' => 7,
            'Content' => 'Necesito actualizar mi plan de hosting. ¿Pueden ayudarme a hacerlo?',
            'user_id' => 5
        ]);

        Message::create([
            'ticket_id' => 8,
            'Content' => 'Estoy teniendo problemas con el certificado SSL. ¿Pueden ayudarme a resolver esto?',
            'user_id' => 5
        ]);

        Message::create([
            'ticket_id' => 9,
            'Content' => 'Necesito ayuda con la configuración de DNS. ¿Pueden proporcionar los pasos necesarios?',
            'user_id' => 5
        ]);

        Message::create([
            'ticket_id' => 10,
            'Content' => 'Estoy teniendo problemas con mi correo electrónico. ¿Pueden ayudarme a resolver esto?',
            'user_id' => 5
        ]);
    }
}
