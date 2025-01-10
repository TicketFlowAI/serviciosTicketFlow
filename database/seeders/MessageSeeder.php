<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    private const MESSAGE_CONTENT = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nec sodales lacus, quis feugiat metus. Donec auctor odio lacus, sit amet eleifend magna semper ut. Cras non ultrices metus. Aliquam tincidunt dui sem, vitae pretium libero consectetur quis. Donec commodo dapibus ex, gravida iaculis risus bibendum a. Curabitur in lacinia tellus. Cras fermentum vehicula ex vitae venenatis. Mauris sit amet odio aliquet, interdum justo ac, sollicitudin risus. Fusce diam odio, malesuada tempor molestie vel';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Message::create([
            'ticket_id' => 1,
            'Content' => self::MESSAGE_CONTENT,
            'user_id' => 2
        ]);
        Message::create([
            'ticket_id' => 1,
            'Content' => self::MESSAGE_CONTENT,
            'user_id' => 3
        ]);
        Message::create([
            'ticket_id' => 1,
            'Content' => self::MESSAGE_CONTENT,
            'user_id' => 2
        ]);
        Message::create([
            'ticket_id' => 1,
            'Content' => self::MESSAGE_CONTENT,
            'user_id' => 3
        ]);
    }
}
