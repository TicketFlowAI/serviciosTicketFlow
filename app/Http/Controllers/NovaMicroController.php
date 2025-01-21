<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NovaMicroController extends Controller
{
    public function handleRequest(Request $request)
    {
        // Capturar la solicitud del usuario desde los parámetros
        $userInput = $request->query('solicitud'); // Para GET
        // Si prefieres POST, usa: $userInput = $request->input('solicitud');

        if (!$userInput) {
            return response()->json(['error' => 'El parámetro "solicitud" es requerido.'], 400);
        }

        // Leer el archivo JSON usando storage_path para obtener la ruta completa
        $filePath = storage_path('app/private/promps/promps.json');

        // Verificar si el archivo existe
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'El archivo JSON no existe.'], 500);
        }

        // Leer el contenido del archivo
        $jsonContent = file_get_contents($filePath);
        $prompts = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Error al decodificar el archivo JSON.'], 500);
        }

        // Buscar el prompt más relevante
        $selectedPrompt = null;
        foreach ($prompts as $prompt) {
            if (stripos($prompt['pregunta'], $userInput) !== false) {
                $selectedPrompt = $prompt;
                break;
            }
        }

        // Crear el contexto para Nova Micro
        $context = "";
        if (!$selectedPrompt) {
            foreach ($prompts as $prompt) {
                $context .= "Pregunta: {$prompt['pregunta']}\nRespuesta: {$prompt['respuesta']}\n\n";
            }
            $context .= "Por favor, responde a esta solicitud basada en la información anterior:\n{$userInput}";
        } else {
            $context = "Pregunta: {$selectedPrompt['pregunta']}\nRespuesta esperada: {$selectedPrompt['respuesta']}";
        }

        // Configurar cliente de Amazon Bedrock
        try {
            $bedrock = new \Aws\Bedrock\BedrockClient([
                'region' => config('aws.region'),
                'version' => 'latest',
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            // Llamar al modelo Nova Micro
            $response = $bedrock->invokeModel([
                'modelId' => 'amazon.nova-micro-v1:0',
                'contentType' => 'application/json',
                'accept' => 'application/json',
                'body' => json_encode([
                    'inferenceConfig' => [
                        'max_new_tokens' => 300,
                        'temperature' => 0.7,
                    ],
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                'text' => $context,
                            ],
                        ],
                    ],
                ]),
            ]);

            // Procesar la respuesta del modelo
            $body = json_decode($response['body']->getContents(), true);

            // Registrar la respuesta en los logs para depuración
            info('Respuesta de Nova Micro: ' . json_encode($body));

            return response()->json(['response' => $body]);

        } catch (\Exception $e) {
            // Registrar el error en los logs para análisis
            info('Error al invocar Nova Micro: ' . $e->getMessage());

            return response()->json(['error' => $e->getMessage()]);
        }
    }
}



