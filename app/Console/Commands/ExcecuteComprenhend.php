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
    protected $description = 'Run classification jobs on Amazon Comprehend using messages content';

    public function handle()
    {
        $this->info('Starting Amazon Comprehend job process...');

        $this->processTickets();

        $this->info('Amazon Comprehend job process completed.');
    }

    protected function processTickets()
    {
        $tickets = Ticket::where('status', 2) // 2 = Proceso
                         ->whereNull('job_id')
                         ->with('message') // Load related messages
                         ->get();

        if ($tickets->isEmpty()) {
            $this->info('No open tickets to process.');
            return;
        }

        foreach ($tickets as $ticket) {
            $this->info("Processing Ticket #{$ticket->id}...");

            if ($ticket->message->isEmpty()) {
                $this->info("No messages found for Ticket #{$ticket->id}. Skipping...");
                continue;
            }

            // Generar CSV y subir a S3
            if (!$this->prepareMessagesForClassification($ticket)) {
                $this->error("Failed to prepare messages for Ticket #{$ticket->id}. Skipping...");
                continue;
            }

            // Ejecutar ambos clasificadores
            $this->startJobs($ticket, 'arn:aws:comprehend:us-east-2:115894170195:document-classifier/PriorityClassifier/version/v1', 's3://comprenhend-dataset/output/classifier/');
            $this->startJobs($ticket, 'arn:aws:comprehend:us-east-2:115894170195:document-classifier/PriorityClassifierHumanIntervention/version/v4', 's3://comprenhend-dataset/output/human intervention/');
        }
    }

    protected function prepareMessagesForClassification(Ticket $ticket): bool
    {
        $csvContent = $ticket->message->map(fn($message) => [
            'MessageID' => $message->id,
            'Content' => $message->content,
        ])->toArray();

        $csvFileName = "input/{$ticket->id}.csv";
        $csvData = $this->convertArrayToCsv($csvContent);

        try {
            if (!Storage::disk('s3')->put($csvFileName, $csvData)) {
                return false;
            }

            $this->info("Successfully uploaded CSV for Ticket #{$ticket->id} to S3.");
            return true;
        } catch (\Exception $e) {
            $this->error("Error uploading CSV for Ticket #{$ticket->id}: " . $e->getMessage());
            return false;
        }
    }

    protected function startJobs(Ticket $ticket, string $classifierArn, string $outputUri)
    {
        $comprehendClient = new ComprehendClient([
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
            ],
        ]);

        $csvFileName = "input/{$ticket->id}.csv";
        $inputUri = "s3://comprenhend-dataset/{$csvFileName}";

        try {
            $response = $comprehendClient->startDocumentClassificationJob([
                'JobName' => 'Job-' . $ticket->id . '-' . basename($outputUri),
                'DocumentClassifierArn' => $classifierArn,
                'InputDataConfig' => [
                    'S3Uri' => $inputUri,
                    'InputFormat' => 'ONE_DOC_PER_LINE',
                ],
                'OutputDataConfig' => [
                    'S3Uri' => $outputUri,
                ],
                'DataAccessRoleArn' => 'arn:aws:iam::115894170195:role/service-role/AmazonComprehendServiceRole-AmazonComprehendServiceRole',
            ]);

            $this->info("Job started for Ticket #{$ticket->id} using classifier {$classifierArn} with Job ID: {$response['JobId']}");
        } catch (\Exception $e) {
            $this->error("Error starting job for Ticket #{$ticket->id} using classifier {$classifierArn}: " . $e->getMessage());
        }
    }

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


