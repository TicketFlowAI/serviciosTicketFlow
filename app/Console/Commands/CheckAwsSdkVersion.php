<?php

namespace App\Console\Commands;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Credentials\Credentials;
use Illuminate\Console\Command;

class CheckAwsSdkVersion extends Command
{
    protected $signature = 'app:check-aws-sdk-version';
    protected $description = 'Check AWS Bedrock Runtime SDK Version';

    public function handle()
    {
        $credentials = new Credentials(
            config('services.aws.key'),
            config('services.aws.secret')
        );

        // Cambia la región a 'us-east-2'
        $BedrockClient = new BedrockRuntimeClient([
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => $credentials,
        ]);

        // Agrega el ARN del perfil de inferencia
        $payload = [
            
            'modelId' => 'us.amazon.nova-micro-v1:0',
            'contentType' => 'application/json',
            'accept' => 'application/json',
            'body' => json_encode([
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'text' => 'Dame una descripción de Jack Sparrow',
                            ],
                        ],
                    ],
                ],
                'inferenceConfig' => [
                    'max_new_tokens' => 1000,
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'top_k' => 50,
                ],
            ]),
        ];

        try {
            // Invocar el modelo
            $response = $BedrockClient->invokeModel($payload);

            // Procesar la respuesta
            $responseBody = $response['body']->getContents();
            $this->info("Respuesta del modelo: " . $responseBody);
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}

