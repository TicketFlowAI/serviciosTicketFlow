<?php

namespace App\Http\Controllers;

use Aws\BedrockAgentRuntime\BedrockAgentRuntimeClient;
use Aws\Signature\SignatureV4;
use Aws\Credentials\Credentials;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NovaMicroController extends Controller
{
    private $region;
    private $credentials;
    private $client;

    public function __construct()
    {
        Log::info('Inicializando NovaMicroController');

        $this->region = env('AWS_REGION', 'us-east-2'); // Cambia según tu región
        $this->credentials = new Credentials(
            env('AWS_ACCESS_KEY_ID'),
            env('AWS_SECRET_ACCESS_KEY')
        );
        $this->client = new BedrockAgentRuntimeClient();

        Log::info('Configuración de AWS inicializada', [
            'region' => $this->region,
        ]);
    }

    public function getResponse(Request $request)
    {
        Log::info('Iniciando el método getResponse');

        // Validar el parámetro recibido
        $userInput = $request->input('solicitud');
        Log::info('Solicitud recibida', ['solicitud' => $userInput]);

        try {
            // Validar si el parámetro es nulo o vacío
            if (empty($userInput)) {
                Log::error('El parámetro solicitud está vacío o no fue enviado');
                return response()->json([
                    'success' => false,
                    'error' => 'El parámetro solicitud es requerido.',
                ], 400);
            }

            // ARN del perfil de inferencia
            $inferenceProfileArn = 'arn:aws:bedrock:us-east-2:115894170195:inference-profile/us.amazon.nova-micro-v1:0';
            Log::info('ARN de inferencia configurado', ['arn' => $inferenceProfileArn]);

            // Construir el endpoint y el path
            $endpoint = "https://bedrock-runtime.{$this->region}.amazonaws.com";
            $path = "/model/amazon.nova-micro-v1:0/invoke";
            Log::info('Endpoint y path configurados', ['endpoint' => $endpoint, 'path' => $path]);

            // Cuerpo de la solicitud
            $body = json_encode([
                'inferenceProfileArn' => $inferenceProfileArn,
                'inputText' => $userInput,
            ]);
            Log::info('Cuerpo de la solicitud creado', ['body' => $body]);

            // Registrar el JSON enviado a Amazon
            Log::info('Enviando JSON a Amazon Bedrock', ['json' => $body]);

            // Crear la firma de la solicitud
            $signature = new SignatureV4('bedrock', $this->region);
            Log::info('Iniciando la firma de la solicitud');

            $requestToSign = new \GuzzleHttp\Psr7\Request(
                'POST',
                $endpoint . $path,
                [
                    'Content-Type' => 'application/json',
                ],
                $body
            );

            $signedRequest = $signature->signRequest($requestToSign, $this->credentials);
            Log::info('Solicitud firmada correctamente', ['headers' => $signedRequest->getHeaders()]);

            // Enviar la solicitud firmada
            Log::info('Enviando la solicitud a Bedrock Runtime', [
                'url' => $endpoint . $path,
                'headers' => $signedRequest->getHeaders(),
                'body' => $body,
            ]);
            $response = $this->client->send($signedRequest);

            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            Log::info('Respuesta recibida de Bedrock Runtime', ['response' => $responseData]);

            return response()->json([
                'success' => true,
                'data' => $responseData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en el método getResponse', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
































