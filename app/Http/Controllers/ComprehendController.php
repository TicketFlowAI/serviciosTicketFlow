<?php

namespace App\Http\Controllers;

use Aws\Comprehend\ComprehendClient;
use Illuminate\Http\Request;

class ComprehendController extends Controller
{
    // Configuración general del cliente AWS Comprehend
    private function getComprehendClient()
    {
        return new ComprehendClient([
            'version' => 'latest',
            'region'  => 'us-east-2', // Cambia a tu región si es diferente
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    // Método para listar todos los clasificadores y sus versiones
    public function listAllClassifiers()
    {
        $client = $this->getComprehendClient();

        try {
            // Llamada al API para listar clasificadores
            $result = $client->listDocumentClassifierSummaries();

            $classifiers = $result['DocumentClassifierSummaries'] ?? [];

            $response = [];
            foreach ($classifiers as $classifier) {
                $classifierArn = $classifier['DocumentClassifierArn'];
                $classifierName = explode('/', $classifierArn)[1]; // Extrae el nombre del clasificador
                $version = last(explode('/', $classifierArn)); // Extrae la versión

                $response[$classifierName][] = [
                    'VersionArn' => $classifierArn,
                    'VersionName' => $version,
                    'Status' => $classifier['Status'] ?? 'Unknown',
                ];
            }

            if (empty($response)) {
                $message = 'No se encontraron clasificadores disponibles.';
                info($message); // Imprime el mensaje en la consola
                return response()->json(['message' => $message], 404);
            }

            info(json_encode($response, JSON_PRETTY_PRINT)); // Imprime el JSON en la consola
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $error = 'Error al listar los clasificadores: ' . $e->getMessage();
            info($error); // Imprime el error en la consola
            return response()->json(['error' => $error], 500);
        }
    }

    // Método para obtener el rendimiento de una versión específica del clasificador
    public function getClassifierPerformance(Request $request)
    {
        // Validar la entrada de la versión
        $request->validate([
            'versionArn' => 'required|string',
        ]);

        $versionArn = $request->input('versionArn');

        $client = $this->getComprehendClient();

        try {
            // Llamada para obtener detalles del clasificador
            $result = $client->describeDocumentClassifier([
                'DocumentClassifierArn' => $versionArn,
            ]);

            // Extraer detalles de performance
            $performanceMetrics = $result['DocumentClassifierProperties']['ClassifierMetadata'] ?? null;

            if (!$performanceMetrics) {
                $message = 'No se encontraron métricas de rendimiento para esta versión.';
                info($message); // Imprime el mensaje en la consola
                return response()->json(['message' => $message], 404);
            }

            $response = [
                'VersionArn' => $versionArn,
                'Accuracy' => $performanceMetrics['EvaluationMetrics']['Accuracy'] ?? 'N/A',
                'F1Score'  => $performanceMetrics['EvaluationMetrics']['F1Score'] ?? 'N/A',
                'Precision' => $performanceMetrics['EvaluationMetrics']['Precision'] ?? 'N/A',
                'Recall'    => $performanceMetrics['EvaluationMetrics']['Recall'] ?? 'N/A',
            ];

            info(json_encode($response, JSON_PRETTY_PRINT)); // Imprime el JSON en la consola
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $error = 'Error al obtener el rendimiento: ' . $e->getMessage();
            info($error); // Imprime el error en la consola
            return response()->json(['error' => $error], 500);
        }
    }

    // Método para entrenar un nuevo clasificador
    public function trainNewClassifierVersion(Request $request)
    {
        // Validar los datos necesarios para entrenar
        $request->validate([
            'classifierName' => 'required|string',
            'inputDataS3Uri' => 'required|url',
            'outputDataS3Uri' => 'required|url',
            'dataAccessRoleArn' => 'required|string',
        ]);

        $classifierName = $request->input('classifierName');
        $inputDataS3Uri = $request->input('inputDataS3Uri');
        $outputDataS3Uri = $request->input('outputDataS3Uri');
        $dataAccessRoleArn = $request->input('dataAccessRoleArn');

        $client = $this->getComprehendClient();

        try {
            // Llamada al API para crear un nuevo clasificador
            $result = $client->createDocumentClassifier([
                'DocumentClassifierName' => $classifierName,
                'DataAccessRoleArn' => $dataAccessRoleArn,
                'InputDataConfig' => [
                    'S3Uri' => $inputDataS3Uri,
                ],
                'OutputDataConfig' => [
                    'S3Uri' => $outputDataS3Uri,
                ],
                'LanguageCode' => 'en',
            ]);

            return response()->json([
                'message' => 'Clasificador creado exitosamente.',
                'ClassifierArn' => $result['DocumentClassifierArn'],
                'Status' => 'TRAINING',
            ], 200);
        } catch (\Exception $e) {
            $error = 'Error al crear el clasificador: ' . $e->getMessage();
            info($error); // Imprime el error en la consola
            return response()->json(['error' => $error], 500);
        }
    }
}
