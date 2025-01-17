<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Aws\Comprehend\ComprehendClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExcecuteComprenhend extends Command
{
    protected $signature = 'app:excecute-comprenhend';
    protected $description = 'Run classification jobs on Amazon Comprehend with single-job processing';

    public function handle()
    {
        $this->info('Starting Amazon Comprehend job process...');

        $this->manageJobs();

        $this->info('Amazon Comprehend job process completed.');
    }

    protected function manageJobs()
    {
        $comprehendClient = new ComprehendClient([
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => [
                'key'    => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
            ],
        ]);

        // Procesar trabajos en progreso
        $inProgressJobs = Ticket::whereNotNull('job_id_classifier')
            ->orWhereNotNull('job_id_human_intervention')
            ->where('status', 2) // 2 = En proceso
            ->get();

        if ($inProgressJobs->isNotEmpty()) {
            $this->info("There are jobs in progress. Checking their status...");

            foreach ($inProgressJobs as $ticket) {
                $this->checkJobStatus($ticket, 'classifier', $comprehendClient);
                $this->checkJobStatus($ticket, 'human intervention', $comprehendClient);
            }

            $this->info('Finished processing in-progress jobs.');
        }

        // Procesar nuevos tickets
        $tickets = Ticket::where('status', 2)
            ->with('message')
            ->get();

        if ($tickets->isEmpty()) {
            $this->info('No tickets to process.');
            return;
        }

        foreach ($tickets as $ticket) {
            $this->info("Processing Ticket #{$ticket->id}...");

            if ($ticket->message->isEmpty()) {
                $this->info("No messages found for Ticket #{$ticket->id}. Skipping...");
                continue;
            }

            $csvUploaded = $this->prepareMessagesForClassification($ticket);
            if (!$csvUploaded) {
                $this->error("Failed to prepare messages for Ticket #{$ticket->id}. Skipping...");
                continue;
            }

            $this->createJobsSequentially($ticket, $comprehendClient);
        }
    }

    protected function checkJobStatus(Ticket $ticket, string $jobType, ComprehendClient $comprehendClient)
    {
        $jobIdField = $jobType === 'classifier' ? 'job_id_classifier' : 'job_id_human_intervention';
        $jobId = $ticket->{$jobIdField};

        if (!$jobId) {
            return;
        }

        try {
            $response = $comprehendClient->describeDocumentClassificationJob([
                'JobId' => $jobId,
            ]);

            $status = $response['DocumentClassificationJobProperties']['JobStatus'];
            $this->info("Ticket #{$ticket->id}, Job Type: {$jobType}, Job ID: {$jobId}, Status: {$status}");

            if ($status === 'COMPLETED') {
                $this->processResults($ticket, $response['DocumentClassificationJobProperties'], $jobType);
            }
        } catch (\Exception $e) {
            $this->error("Error checking job status for Ticket #{$ticket->id}, Job Type: {$jobType}: " . $e->getMessage());
        }
    }

    protected function processResults(Ticket $ticket, array $jobProps, string $jobType)
    {
        $jobIdField = $jobType === 'classifier' ? 'job_id_classifier' : 'job_id_human_intervention';
        $jobId = $ticket->{$jobIdField};

        $fullS3Uri = $jobProps['OutputDataConfig']['S3Uri'];
        $bucketPrefix = 's3://' . env('AWS_BUCKET') . '/';
        $relativePath = Str::after($fullS3Uri, $bucketPrefix);

        $this->info("Processing file for Ticket #{$ticket->id}, Job Type: {$jobType}.");

        if (!Storage::disk('s3')->exists($relativePath)) {
            $this->error("Result file not found in S3: {$relativePath}");
            return;
        }

        try {
            $fileContents = Storage::disk('s3')->get($relativePath);

            $baseTempPath = '/home/servicios/htdocs/servicios.mindsoftdev.com/storage/app/private/temp';
            $localDir = "{$baseTempPath}/{$jobId}";

            if (!file_exists($localDir)) {
                mkdir($localDir, 0775, true);
                $this->info("Directory created: {$localDir}");
            }

            $localTarGzPath = "{$localDir}/output.tar.gz";
            file_put_contents($localTarGzPath, $fileContents);

            $this->info("File downloaded and stored locally at: {$localTarGzPath}");

            $this->extractTarGz($localTarGzPath, $localDir);

            $jsonFilePath = "{$localDir}/predictions.jsonl";

            if (!file_exists($jsonFilePath)) {
                $this->error("Extracted JSONL file not found for Ticket #{$ticket->id}, Job Type: {$jobType}.");
                return;
            }

            $this->info("Found extracted JSONL file for Ticket #{$ticket->id} at: {$jsonFilePath}");

            $lines = file($jsonFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (count($lines) < 2) {
                $this->error("Not enough lines in JSONL file for Ticket #{$ticket->id}.");
                return;
            }

            $secondLine = json_decode($lines[1], true);

            if (!$secondLine) {
                $this->error("Failed to decode the second line of JSONL for Ticket #{$ticket->id}.");
                return;
            }

            if ($jobType === 'classifier') {
                $this->updatePriority($ticket, $secondLine);
            } elseif ($jobType === 'human intervention') {
                $this->updateNeedsHumanInteraction($ticket, $secondLine);
            }

            $this->info("Results processed successfully for Ticket #{$ticket->id}, Job Type: {$jobType}.");
        } catch (\Exception $e) {
            $this->error("Error processing results for Ticket #{$ticket->id}, Job Type: {$jobType}: " . $e->getMessage());
        }
    }

    protected function extractTarGz(string $tarFilePath, string $extractToDir)
    {
        $this->info("Extracting: {$tarFilePath}");

        if (!file_exists($tarFilePath)) {
            throw new \Exception("File not found: {$tarFilePath}");
        }

        try {
            $tarPath = str_replace('.gz', '', $tarFilePath);

            if (file_exists($tarPath)) {
                unlink($tarPath);
                $this->info("Existing tar file deleted: {$tarPath}");
            }

            $phar = new \PharData($tarFilePath);
            $phar->decompress();

            if (!file_exists($tarPath)) {
                throw new \Exception("Decompressed .tar file not found: {$tarPath}");
            }

            $pharTar = new \PharData($tarPath);
            $pharTar->extractTo($extractToDir);

            $this->info("Extraction completed successfully to: {$extractToDir}");
        } catch (\Exception $e) {
            throw new \Exception("Error extracting {$tarFilePath}: " . $e->getMessage());
        }
    }

    protected function prepareMessagesForClassification(Ticket $ticket): bool
    {
        $csvContent = $ticket->message->map(fn($message) => [
            'MessageID' => $message->id,
            'Content'   => $message->content,
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

    protected function updatePriority(Ticket $ticket, array $results)
    {
        $labels = $results['Labels'] ?? [];
        foreach ($labels as $label) {
            if (str_contains($label['Name'], 'Prioridad')) {
                $priorityValue = (int) filter_var($label['Name'], FILTER_SANITIZE_NUMBER_INT);
                $ticket->priority = $priorityValue;
                $ticket->save();

                $this->info("Updated priority for Ticket #{$ticket->id} to {$priorityValue}");
                return;
            }
        }

        $this->warn("No priority value found in results for Ticket #{$ticket->id}");
    }

    protected function updateNeedsHumanInteraction(Ticket $ticket, array $results)
    {
        $classes = $results['Classes'] ?? [];
        $highestScore = 0;
        $selectedClass = null;

        foreach ($classes as $class) {
            if ($class['Score'] > $highestScore) {
                $highestScore = $class['Score'];
                $selectedClass = $class['Name'];
            }
        }

        if ($selectedClass !== null) {
            $needsHumanInteraction = ($selectedClass === 'sí' || $selectedClass === 'Etiqueta') ? 1 : 0;
            $ticket->needsHumanInteraction = $needsHumanInteraction;
            $ticket->save();

            $this->info("Updated needsHumanInteraction for Ticket #{$ticket->id} to {$needsHumanInteraction}");
        } else {
            $this->warn("No valid class found in results for Ticket #{$ticket->id}");
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

    protected function createJobsSequentially(Ticket $ticket, ComprehendClient $comprehendClient)
    {
        if (!$ticket->job_id_classifier) {
            $jobId = $this->startJob(
                $ticket,
                'arn:aws:comprehend:us-east-2:115894170195:document-classifier/PriorityClassifier/version/v1',
                's3://comprenhend-dataset/output/classifier/',
                $comprehendClient
            );
            if ($jobId) {
                $ticket->update(['job_id_classifier' => $jobId]);
            }
        }

        if (!$ticket->job_id_human_intervention) {
            $jobId = $this->startJob(
                $ticket,
                'arn:aws:comprehend:us-east-2:115894170195:document-classifier/PriorityClassifierHumanIntervention/version/v4',
                's3://comprenhend-dataset/output/human_intervention/',
                $comprehendClient
            );
            if ($jobId) {
                $ticket->update(['job_id_human_intervention' => $jobId]);
            }
        }

        $ticket->update(['status' => 1]);
    }

    protected function startJob(Ticket $ticket, string $classifierArn, string $outputUri, ComprehendClient $comprehendClient): ?string
    {
        $csvFileName = "input/{$ticket->id}.csv";
        $inputUri = "s3://comprenhend-dataset/{$csvFileName}";

        $maxAttempts = 5;
        $attempt = 0;
        $backoff = 1;

        while ($attempt < $maxAttempts) {
            try {
                $response = $comprehendClient->startDocumentClassificationJob([
                    'JobName'               => 'Job-' . $ticket->id,
                    'DocumentClassifierArn' => $classifierArn,
                    'InputDataConfig'       => [
                        'S3Uri'       => $inputUri,
                        'InputFormat' => 'ONE_DOC_PER_LINE',
                    ],
                    'OutputDataConfig' => [
                        'S3Uri' => $outputUri,
                    ],
                    'DataAccessRoleArn' => 'arn:aws:iam::115894170195:role/service-role/AmazonComprehendServiceRole-AmazonComprehendServiceRole',
                ]);

                $this->info("Job started for Ticket #{$ticket->id}, Job Type: {$classifierArn}, Job ID: {$response['JobId']}");
                return $response['JobId'];

            } catch (\Aws\Exception\AwsException $e) {
                if ($e->getAwsErrorCode() === 'ThrottlingException') {
                    $attempt++;
                    $this->warn("ThrottlingException (Rate exceeded) for Ticket #{$ticket->id}, Job Type: {$classifierArn}. Attempt {$attempt} of {$maxAttempts}. Retrying in {$backoff}s...");
                    sleep($backoff);
                    $backoff *= 2;
                } else {
                    $this->error("Error starting job for Ticket #{$ticket->id}, Job Type: {$classifierArn}: " . $e->getMessage());
                    return null;
                }
            }
        }

        $this->error("Max attempts reached for Ticket #{$ticket->id}, Job Type: {$classifierArn}. Job could not be started.");
        return null;
    }
}
