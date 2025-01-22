<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use Aws\Comprehend\ComprehendClient;
use Illuminate\Http\Request;
use App\Http\Resources\ClassifierResource;
use App\Http\Resources\ClassifierPerformanceResource;
use App\Http\Resources\NewClassifierResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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

            // Llamada al método correcto para listar clasificadores
            $result = $client->listDocumentClassifiers();



            // Extraer la lista de clasificadores
            $classifiers = $result['DocumentClassifierPropertiesList'] ?? [];


            // Manejar el caso en que no haya clasificadores
            if (empty($classifiers)) {
                $message = 'No se encontraron clasificadores disponibles.';
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

    public function updateClassifierArns(Request $request)
    {
        // Validar la entrada
        $request->validate([
            'priority_classifier_arn' => 'nullable|string',
            'human_intervention_classifier_arn' => 'nullable|string',
        ]);
    
        // Ruta absoluta al archivo de configuración
        $configPath = '/home/servicios/htdocs/servicios.mindsoftdev.com/serviciosTicketFlow/config/classifiers.php';
    
        // Log: Ruta del archivo
        Log::info("Using configuration file path: {$configPath}");
    
        // Depuración: Verificar si el archivo existe
        if (!File::exists($configPath)) {
            Log::error("Configuration file not found at path: {$configPath}");
            return response()->json(['message' => 'Configuration file not found.'], 500);
        }
    
        // Leer el archivo de configuración actual
        try {
            $currentConfig = include($configPath);
            Log::info("Successfully loaded current configuration: ", $currentConfig);
        } catch (\Exception $e) {
            Log::error("Failed to load configuration file: {$e->getMessage()}");
            return response()->json(['message' => 'Failed to load configuration file.'], 500);
        }
    
        // Actualizar la configuración con los valores recibidos
        $updatedConfig = array_merge($currentConfig, array_filter([
            'priority_classifier_arn' => $request->input('priority_classifier_arn'),
            'human_intervention_classifier_arn' => $request->input('human_intervention_classifier_arn'),
        ]));
    
        Log::info("Updated configuration to be written: ", $updatedConfig);
    
        // Intentar sobrescribir el archivo de configuración
        try {
            File::put($configPath, "<?php\n\nreturn " . var_export($updatedConfig, true) . ";");
            Log::info("Configuration file updated successfully at path: {$configPath}");
        } catch (\Exception $e) {
            Log::error("Failed to write to configuration file: {$e->getMessage()}");
            return response()->json(['message' => 'Failed to update configuration file.'], 500);
        }
    
        return response()->json(['message' => 'Classifiers updated successfully.']);
    }

}



