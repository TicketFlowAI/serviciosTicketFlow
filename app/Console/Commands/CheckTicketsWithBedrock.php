<?php

namespace App\Console\Commands;

use App\Http\Controllers\MessageController;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Credentials\Credentials;
use Illuminate\Console\Command;
use App\Models\Ticket;
use Illuminate\Support\Facades\App;
use \App\Http\Controllers\TicketController;
use Log;

class CheckTicketsWithBedrock extends Command
{
    protected $signature = 'app:check-tickets-bedrock';
    protected $description = 'Check tickets with specific attributes and query Amazon Bedrock';

    public function handle()
    {
        $credentials = new Credentials(
            config('services.aws.key'),
            config('services.aws.secret')
        );

        $BedrockClient = new BedrockRuntimeClient([
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => $credentials,
        ]);

        $tickets = Ticket::whereNotNull('priority')
            ->whereNotNull('needsHumanInteraction')
            ->where('complexity', 1)
            ->where('AIresponse', 0)
            ->with('message')
            ->get();

        $tickets2  = Ticket::whereNotNull('priority')
        ->whereNotNull('needsHumanInteraction')
        ->where('complexity','!=', 1)
        ->get();

        if ($tickets2->isNotEmpty()) {
            foreach ($tickets2 as $ticket) {
                $ticketController = App::make(TicketController::class);
                $ticketController->assignTicket($ticket->id);
            }
            return;
        }


        if ($tickets->isEmpty()) {
            $this->info('No tickets found with the required attributes.');
            return;
        }

        $prompsPath = '/home/servicios/htdocs/servicios.mindsoftdev.com/serviciosTicketFlow/storage/app/private/promps/promps.json';

        if (!file_exists($prompsPath)) {
            $this->error("No se encontró el archivo de promps en {$prompsPath}");
            return;
        }

        $rawPrompsContent = file_get_contents($prompsPath);

        if (empty($rawPrompsContent)) {
            $this->error("El archivo de promps está vacío.");
            return;
        }

        $promps = json_decode($rawPrompsContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Error al decodificar el JSON: " . json_last_error_msg());
            return;
        }

        foreach ($tickets as $ticket) {
            $message = $ticket->message->first();

            if (!$message) {
                $this->warn("Ticket #{$ticket->id} has no associated messages. Skipping...");
                continue;
            }

            $userInput = $message->content;

            $similarPromp = collect($promps)->first(function ($promp) use ($userInput) {
                return stripos($userInput, $promp['pregunta']) !== false;
            });

            if ($similarPromp) {
                $context = [
                    [
                        'role' => 'user',
                        'content' => [["text" => "Usa la siguiente información como contexto:\nPregunta: {$similarPromp['pregunta']}\nRespuesta: {$similarPromp['respuesta']}"]]
                    ],
                    [
                        'role' => 'user',
                        'content' => [["text" => $userInput]]
                    ]
                ];
            } else {
                $context = collect($promps)->map(function ($promp) {
                    return [
                        'role' => 'user',
                        'content' => [["text" => "Pregunta: {$promp['pregunta']}\nRespuesta: {$promp['respuesta']}"]]
                    ];
                })->toArray();

                $context[] = [
                    'role' => 'user',
                    'content' => [["text" => $userInput]]
                ];
            }

            $payload = [
                'modelId' => 'us.amazon.nova-micro-v1:0',
                'contentType' => 'application/json',
                'accept' => 'application/json',
                'body' => json_encode([
                    'messages' => $context,
                    'inferenceConfig' => [
                        'max_new_tokens' => 1000,
                        'temperature' => 0.7,
                        'top_p' => 0.9,
                        'top_k' => 50,
                    ],
                ]),
            ];

            try {
                $response = $BedrockClient->invokeModel($payload);
                $responseBody = $response['body']->getContents();

                // Extraer solo el contenido del texto como string
                $responseArray = json_decode($responseBody, true);
                $textContent = $responseArray['output']['message']['content'][0]['text'] ?? '';

                // Guardar la respuesta como un mensaje AI usando el contenedor de Laravel
                $messageController = App::make(MessageController::class);
                $messageController->createAIMessage($ticket->id, (string)$textContent);

            } catch (\Exception $e) {
                $this->error("Error processing Ticket #{$ticket->id}: " . $e->getMessage());
            }
        }
    }
}


