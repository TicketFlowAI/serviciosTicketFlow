<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Aws\Comprehend\ComprehendClient;
use Illuminate\Support\Facades\Storage;

class ExcecuteComprenhend extends Command
{
    protected $signature = 'app:excecute-comprenhend';
    protected $description = 'Run a classification job on Amazon Comprehend using messages content';

    public function handle()
    {
        $this->info('Starting Amazon Comprehend job process...');
        $this->prepareMessagesForClassification();
        $this->startJobs();
        $this->checkJobStatus();
        $this->info('Amazon Comprehend job process completed.');
    }

    /**
     * Prepare messages for classification by creating CSV files and uploading them to S3.
     */
    protected function prepareMessagesForClassification()
    {
        $tickets = Ticket::where('status', 1) // 1 = Abierto
                         ->whereNull('job_id')
                         ->with('message') // Load related messages
                         ->get();

        if ($tickets->isEmpty()) {
            $this->info('No open tickets to prepare messages for classification.');
            return;
        }

        foreach ($tickets as $ticket) {
            if ($ticket->message->isEmpty()) {
                $this->info("No messages found for Ticket #{$ticket->id}. Skipping...");
                continue; // Salta este ticket si no tiene mensajes
            }

            // Crear contenido CSV
            $csvContent = $ticket->message->map(fn($message) => [
                'MessageID' => $message->id,
                'Content' => $message->content,
            ])->toArray();

            $csvFileName = "input/{$ticket->id}.csv";
            $csvData = $this->convertArrayToCsv($csvContent);

            // Subir a S3
            if (!Storage::disk('s3')->put($csvFileName, $csvData)) {
                $this->error("Failed to upload CSV for Ticket #{$ticket->id}");
                continue; // Salta si no pudo subir el archivo
            }

            $this->info("Successfully uploaded CSV for Ticket #{$ticket->id} to S3.");
        }
    }

    /**
     * Start classification jobs for tickets with uploaded messages.
     */
    protected function startJobs()
    {
        $tickets = Ticket::where('status', 1) // 1 = Abierto
                         ->whereNull('job_id')
                         ->get();

        if ($tickets->isEmpty()) {
            $this->info('No open tickets to start jobs.');
            return;
        }

        $comprehendClient = new ComprehendClient([
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
            ],
        ]);

        foreach ($tickets as $ticket) {
            $csvFileName = "input/{$ticket->id}.csv";
            if (!Storage::disk('s3')->exists($csvFileName)) {
                $this->error("No CSV file found for Ticket #{$ticket->id}, skipping job creation.");
                continue; // Salta si el archivo CSV no existe
            }

            try {
                $inputUri = "s3://comprenhend-dataset/{$csvFileName}";
                $outputUri = 's3://comprenhend-dataset/output/classifier/';
                $arn = $ticket->needsHumanInteraction
                    ? 'arn:aws:comprehend:us-east-2:115894170195:document-classifier/PriorityClassifierHumanIntervention/version/v4'
                    : 'arn:aws:comprehend:us-east-2:115894170195:document-classifier/PriorityClassifier/version/v1';

                $response = $comprehendClient->startDocumentClassificationJob([
                    'JobName' => 'Job-' . $ticket->id,
                    'DocumentClassifierArn' => $arn,
                    'InputDataConfig' => [
                        'S3Uri' => $inputUri,
                        'InputFormat' => 'ONE_DOC_PER_LINE',
                    ],
                    'OutputDataConfig' => [
                        'S3Uri' => $outputUri,
                    ],
                    'DataAccessRoleArn' => 'arn:aws:iam::115894170195:role/service-role/AmazonComprehendServiceRole-AmazonComprehendServiceRole',
                ]);

                $ticket->update(['job_id' => $response['JobId']]);
                $this->info("Job started for Ticket #{$ticket->id} with Job ID: {$response['JobId']}");
            } catch (\Exception $e) {
                $this->error("Error processing Ticket #{$ticket->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Check the status of classification jobs and process results.
     */
    protected function checkJobStatus()
    {
        $tickets = Ticket::where('status', 1) // 1 = Abierto
                         ->whereNotNull('job_id')
                         ->get();

        if ($tickets->isEmpty()) {
            $this->info('No jobs to check.');
            return;
        }

        $comprehendClient = new ComprehendClient([
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
            ],
        ]);

        foreach ($tickets as $ticket) {
            try {
                $response = $comprehendClient->describeDocumentClassificationJob([
                    'JobId' => $ticket->job_id,
                ]);

                $status = $response['DocumentClassificationJobProperties']['JobStatus'];
                $this->info("Ticket #{$ticket->id}, Job ID {$ticket->job_id}, Status: {$status}");

                if ($status === 'COMPLETED') {
                    $this->processResults($ticket, $response['DocumentClassificationJobProperties']);
                }
            } catch (\Exception $e) {
                $this->error("Error checking status for Ticket #{$ticket->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Process classification results from completed jobs.
     */
    protected function processResults(Ticket $ticket, array $jobProps)
    {
        $outputUri = $jobProps['OutputDataConfig']['S3Uri'];
        $outputDir = str_replace('s3://', '', $outputUri);
        $files = Storage::disk('s3')->files($outputDir);
        $resultFile = collect($files)->first(fn($file) => str_ends_with($file, '.json'));

        if (!$resultFile) {
            $this->error("No result file found for Ticket #{$ticket->id}");
            return;
        }

        $resultContent = Storage::disk('s3')->get($resultFile);
        $results = json_decode($resultContent, true);

        $ticket->update(['status' => 2]); // 2 = En proceso
        $this->info("Results processed for Ticket #{$ticket->id}");
    }

    /**
     * Convert array to CSV format.
     */
    protected function convertArrayToCsv(array $data): string
    {
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($csv, $row);
        }
        rewind($csv);
        $csvData = stream_get_contents($csv);
        fclose($csv);

        return $csvData;
    }
}


