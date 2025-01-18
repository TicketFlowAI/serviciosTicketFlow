<?php

namespace App\Http\Controllers;

use Aws\Comprehend\ComprehendClient;
use Illuminate\Http\Request;

class ClassifierController extends Controller
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

    /**
     * @OA\Get(
     *     path="/classifiers",
     *     summary="List all classifiers and their versions",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\AdditionalProperties(
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="VersionArn", type="string"),
     *                     @OA\Property(property="VersionName", type="string"),
     *                     @OA\Property(property="Status", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No classifiers found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error listing classifiers"
     *     )
     * )
     */
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
                return response()->json([
                    'message' => 'No se encontraron clasificadores disponibles.',
                ], 404);
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al listar los clasificadores: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/classifiers/performance",
     *     summary="Get performance of a specific classifier version",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="versionArn", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="VersionArn", type="string"),
     *             @OA\Property(property="Accuracy", type="string"),
     *             @OA\Property(property="F1Score", type="string"),
     *             @OA\Property(property="Precision", type="string"),
     *             @OA\Property(property="Recall", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No performance metrics found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error getting performance"
     *     )
     * )
     */
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
                return response()->json([
                    'message' => 'No se encontraron métricas de rendimiento para esta versión.',
                ], 404);
            }

            return response()->json([
                'VersionArn' => $versionArn,
                'Accuracy' => $performanceMetrics['EvaluationMetrics']['Accuracy'] ?? 'N/A',
                'F1Score'  => $performanceMetrics['EvaluationMetrics']['F1Score'] ?? 'N/A',
                'Precision' => $performanceMetrics['EvaluationMetrics']['Precision'] ?? 'N/A',
                'Recall'    => $performanceMetrics['EvaluationMetrics']['Recall'] ?? 'N/A',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el rendimiento: ' . $e->getMessage(),
            ], 500);
        }
    }
}

