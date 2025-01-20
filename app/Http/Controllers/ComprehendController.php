<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use Aws\Comprehend\ComprehendClient;
use Illuminate\Http\Request;
use App\Http\Resources\ClassifierResource;
use App\Http\Resources\ClassifierPerformanceResource;
use App\Http\Resources\NewClassifierResource;

use Illuminate\Support\Facades\Auth;

class ComprehendController extends Controller
{
    // Configuración general del cliente AWS Comprehend
    private function getComprehendClient()
    {
        return new ComprehendClient([
            'version' => 'latest',
            'region'  => 'us-east-2', // Cambia según tu región
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    // Método para listar todos los clasificadores y sus versiones
    public function listAllClassifiers()
    {
        try {
            $client = $this->getComprehendClient();

            // Log antes de llamar a AWS
            info('Iniciando llamada a AWS Comprehend para listar clasificadores.');

            // Llamada al método correcto para listar clasificadores
            $result = $client->listDocumentClassifiers();

            // Log completo de la respuesta para depuración
            info('Respuesta completa de AWS: ' . json_encode($result, JSON_PRETTY_PRINT));

            // Extraer la lista de clasificadores
            $classifiers = $result['DocumentClassifierPropertiesList'] ?? [];

            // Log para ver el contenido de classifiers
            info('Contenido de classifiers: ' . json_encode($classifiers, JSON_PRETTY_PRINT));

            // Manejar el caso en que no haya clasificadores
            if (empty($classifiers)) {
                $message = 'No se encontraron clasificadores disponibles.';
                info($message); // Imprimir el mensaje en el log
                return ApiResponseClass::sendResponse(null, $message, 404);
            }

            // Construir la respuesta
            $response = [];
            foreach ($classifiers as $classifier) {
                // Extraer el nombre del clasificador desde el ARN
                $arnParts = explode('/', $classifier['DocumentClassifierArn']);
                $classifierName = $arnParts[1] ?? 'N/A';

                $response[] = [
                    'ClassifierName' => $classifierName, // Agregar el nombre del clasificador
                    'ClassifierArn' => $classifier['DocumentClassifierArn'],
                    'VersionName' => $classifier['VersionName'] ?? 'N/A',
                    'Status' => $classifier['Status'],
                    'LanguageCode' => $classifier['LanguageCode'],
                    'SubmitTime' => $classifier['SubmitTime'],
                    'EndTime' => $classifier['EndTime'] ?? null,
                    'NumberOfLabels' => $classifier['ClassifierMetadata']['NumberOfLabels'] ?? 0,
                    'Accuracy' => $classifier['ClassifierMetadata']['EvaluationMetrics']['Accuracy'] ?? 'N/A',
                    'F1Score' => $classifier['ClassifierMetadata']['EvaluationMetrics']['F1Score'] ?? 'N/A',
                    'Precision' => $classifier['ClassifierMetadata']['EvaluationMetrics']['Precision'] ?? 'N/A',
                    'Recall' => $classifier['ClassifierMetadata']['EvaluationMetrics']['Recall'] ?? 'N/A',
                ];
            }

            info('Respuesta formateada: ' . json_encode($response, JSON_PRETTY_PRINT)); // Log para depuración
            return ApiResponseClass::sendResponse(($response), '', 200);

        } catch (\Exception $e) {
            $error = 'Error al listar los clasificadores: ' . $e->getMessage();
            info($error); // Log para depuración
            return ApiResponseClass::sendResponse(null, $error, 500);
        }
    }

    // Método para obtener el rendimiento de una versión específica del clasificador
    public function getClassifierPerformance(Request $request)
    {
        try {
            // Validar la entrada de la versión
            $request->validate([
                'versionArn' => 'required|string',
            ]);

            $versionArn = $request->input('versionArn');

            $client = $this->getComprehendClient();

            // Llamada para obtener detalles del clasificador
            $result = $client->describeDocumentClassifier([
                'DocumentClassifierArn' => $versionArn,
            ]);

            // Log completo de la respuesta para depuración
            info('Respuesta completa de describeDocumentClassifier: ' . json_encode($result, JSON_PRETTY_PRINT));

            // Extraer detalles de performance
            $performanceMetrics = $result['DocumentClassifierProperties']['ClassifierMetadata'] ?? null;

            if (!$performanceMetrics) {
                $message = 'No se encontraron métricas de rendimiento para esta versión.';
                info($message); // Imprime el mensaje en la consola
                return ApiResponseClass::sendResponse(null, $message, 404);
            }

            $response = [
                'VersionArn' => $versionArn,
                'Accuracy' => $performanceMetrics['EvaluationMetrics']['Accuracy'] ?? 'N/A',
                'F1Score'  => $performanceMetrics['EvaluationMetrics']['F1Score'] ?? 'N/A',
                'Precision' => $performanceMetrics['EvaluationMetrics']['Precision'] ?? 'N/A',
                'Recall'    => $performanceMetrics['EvaluationMetrics']['Recall'] ?? 'N/A',
            ];

            info('Rendimiento del clasificador: ' . json_encode($response, JSON_PRETTY_PRINT)); // Log para depuración
            return ApiResponseClass::sendResponse(($response), '', 200);
        } catch (\Exception $e) {
            $error = 'Error al obtener el rendimiento: ' . $e->getMessage();
            info($error); // Imprime el error en la consola
            return ApiResponseClass::sendResponse(null, $error, 500);
        }
    }

    // Método para entrenar un nuevo clasificador
    public function trainNewClassifierVersion(Request $request)
    {
        try {
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

            info('Respuesta de creación del clasificador: ' . json_encode($result, JSON_PRETTY_PRINT)); // Log para depuración

            return ApiResponseClass::sendResponse(($result), 'Clasificador creado exitosamente.', 200);
        } catch (\Exception $e) {
            $error = 'Error al crear el clasificador: ' . $e->getMessage();
            info($error); // Imprime el error en la consola
            return ApiResponseClass::sendResponse(null, $error, 500);
        }
    }
}



