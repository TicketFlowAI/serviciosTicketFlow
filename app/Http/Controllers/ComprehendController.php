<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use Aws\Comprehend\ComprehendClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ComprehendController extends Controller
{
    // General configuration of the AWS Comprehend client
    private function getComprehendClient()
    {
        return new ComprehendClient([
            'version' => 'latest',
            'region' => 'us-east-2', // Change according to your region
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    // Method to list all classifiers and their versions
    public function listAllClassifiers()
    {
        try {
            $client = $this->getComprehendClient();

            // Call the correct method to list classifiers
            $result = $client->listDocumentClassifiers();

            // Extract the list of classifiers
            $classifiers = $result['DocumentClassifierPropertiesList'] ?? [];

            // Handle the case where there are no classifiers
            if (empty($classifiers)) {
                $message = 'No classifiers found.';
                return ApiResponseClass::sendResponse(null, $message, 404);
            }

            // Build the response
            $response = [];
            foreach ($classifiers as $classifier) {
                // Extract the classifier name from the ARN
                $arnParts = explode('/', $classifier['DocumentClassifierArn']);
                $classifierName = $arnParts[1] ?? 'N/A';

                $response[] = [
                    'ClassifierName' => $classifierName, // Add the classifier name
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
            $error = 'Error listing classifiers: ' . $e->getMessage();
            info($error); // Log for debugging
            return ApiResponseClass::sendResponse(null, $error, 500);
        }
    }

    // Method to get the performance of a specific classifier version
    public function getClassifierPerformance(Request $request)
    {
        try {
            // Validate the version input
            $request->validate([
                'versionArn' => 'required|string',
            ]);

            $versionArn = $request->input('versionArn');

            $client = $this->getComprehendClient();

            // Call to get classifier details
            $result = $client->describeDocumentClassifier([
                'DocumentClassifierArn' => $versionArn,
            ]);

            // Full response log for debugging
            info('Full response from describeDocumentClassifier: ' . json_encode($result, JSON_PRETTY_PRINT));

            // Extract performance details
            $performanceMetrics = $result['DocumentClassifierProperties']['ClassifierMetadata'] ?? null;

            if (!$performanceMetrics) {
                $message = 'No performance metrics found for this version.';
                info($message); // Print the message to the console
                return ApiResponseClass::sendResponse(null, $message, 404);
            }

            $response = [
                'VersionArn' => $versionArn,
                'Accuracy' => $performanceMetrics['EvaluationMetrics']['Accuracy'] ?? 'N/A',
                'F1Score' => $performanceMetrics['EvaluationMetrics']['F1Score'] ?? 'N/A',
                'Precision' => $performanceMetrics['EvaluationMetrics']['Precision'] ?? 'N/A',
                'Recall' => $performanceMetrics['EvaluationMetrics']['Recall'] ?? 'N/A',
            ];

            info('Classifier performance: ' . json_encode($response, JSON_PRETTY_PRINT)); // Log for debugging
            return ApiResponseClass::sendResponse(($response), '', 200);
        } catch (\Exception $e) {
            $error = 'Error getting performance: ' . $e->getMessage();
            info($error); // Print the error to the console
            return ApiResponseClass::sendResponse(null, $error, 500);
        }
    }

    // Method to train a new classifier
    public function trainNewClassifierVersion(Request $request)
    {
        try {
            // Validate the necessary data for training
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

            // Call the API to create a new classifier
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

            info('Classifier creation response: ' . json_encode($result, JSON_PRETTY_PRINT)); // Log for debugging

            return ApiResponseClass::sendResponse(($result), 'Classifier created successfully.', 200);
        } catch (\Exception $e) {
            $error = 'Error creating classifier: ' . $e->getMessage();
            info($error); // Print the error to the console
            return ApiResponseClass::sendResponse(null, $error, 500);
        }
    }

    public function updateClassifierArns(Request $request)
    {
        // Validate the input
        $request->validate([
            'priority_classifier_arn' => 'nullable|string',
            'human_intervention_classifier_arn' => 'nullable|string',
        ]);

        // Absolute path to the configuration file
        $configPath = '/home/servicios/htdocs/servicios.mindsoftdev.com/serviciosTicketFlow/config/classifiers.php';

        // Debugging: Check if the file exists
        if (!File::exists($configPath)) {
            return response()->json(['message' => 'Configuration file not found.'], 500);
        }

        // Read the current configuration file
        try {
            $currentConfig = include($configPath);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load configuration file.'], 500);
        }

        // Update the configuration with the received values
        $updatedConfig = array_merge($currentConfig, array_filter([
            'priority_classifier_arn' => $request->input('priority_classifier_arn'),
            'human_intervention_classifier_arn' => $request->input('human_intervention_classifier_arn'),
        ]));

        // Attempt to overwrite the configuration file
        try {
            File::put($configPath, "<?php\n\nreturn " . var_export($updatedConfig, true) . ";");
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update configuration file.'], 500);
        }

        return response()->json(['message' => 'Classifiers updated successfully.']);
    }

}



