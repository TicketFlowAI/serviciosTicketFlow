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

        // 1. Revisamos si hay jobs en progreso
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

        // 2. Procesamos los nuevos tickets
        $tickets = Ticket::where('status', 2) // 2 = En proceso
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

    protected function prepareMessagesForClassification(Ticket $ticket): bool
    {
        $csvContent = $ticket->message->map(fn($message) => [
            'MessageID' => $message->id,
            'Content'   => $message->content,
        ])->toArray();

        $csvFileName = "input/{$ticket->id}.csv";
        $csvData     = $this->convertArrayToCsv($csvContent);

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

    protected function createJobsSequentially(Ticket $ticket, ComprehendClient $comprehendClient): void
    {
        // Primero creamos el job "classifier" si no existe
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

        // Luego creamos el job "human intervention" si no existe
        if (!$ticket->job_id_human_intervention) {
            $jobId = $this->startJob(
                $ticket,
                'arn:aws:comprehend:us-east-2:115894170195:document-classifier/PriorityClassifierHumanIntervention/version/v4',
                's3://comprenhend-dataset/output/human intervention/',
                $comprehendClient
            );
            if ($jobId) {
                $ticket->update(['job_id_human_intervention' => $jobId]);
            }
        }

        // Finalmente, marcamos el ticket como "Abierto" (status = 1)
        $ticket->update(['status' => 1]);
    }

    protected function startJob(Ticket $ticket, string $classifierArn, string $outputUri, ComprehendClient $comprehendClient): ?string
    {
        $csvFileName = "input/{$ticket->id}.csv";
        $inputUri    = "s3://comprenhend-dataset/{$csvFileName}";

        // Parámetros de reintento
        $attempts     = 0;
        $maxAttempts  = 5;   // máximo de reintentos
        $backoff      = 1;   // segundos de espera inicial

        while ($attempts < $maxAttempts) {
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
                // Detecta si es ThrottlingException
                if ($e->getAwsErrorCode() === 'ThrottlingException') {
                    $attempts++;
                    $this->warn("ThrottlingException (Rate exceeded) al iniciar job: intento #{$attempts} de {$maxAttempts}. Esperando {$backoff}s...");
                    sleep($backoff);
                    $backoff *= 2;
                } else {
                    // Otro tipo de error, no reintentamos
                    $this->error("Error starting job for Ticket #{$ticket->id}, Job Type: {$classifierArn}: " . $e->getMessage());
                    return null;
                }
            }
        }

        $this->error("Max reintentos agotados para iniciar job en Ticket #{$ticket->id}, Job Type: {$classifierArn}");
        return null;
    }

    protected function checkJobStatus(Ticket $ticket, string $jobType, ComprehendClient $comprehendClient)
    {
        $jobIdField = $jobType === 'classifier' ? 'job_id_classifier' : 'job_id_human_intervention';
        $jobId      = $ticket->{$jobIdField};

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
        $jobId      = $ticket->{$jobIdField};

        // Comprehend, en "OutputDataConfig", puede devolver la ruta COMPLETA con output.tar.gz
        $fullS3Uri = $jobProps['OutputDataConfig']['S3Uri'];
        $this->info("Comprehend gave me: {$fullS3Uri}");

        // 1) Quitar la parte "s3://comprenhend-dataset/" para usarlo como path relativo en disk('s3')
        $bucketPrefix = 's3://'.env('AWS_BUCKET').'/';
        $relativePath = Str::after($fullS3Uri, $bucketPrefix);

        $this->info("Relative path to check: {$relativePath}");

        // 2) Listar archivos en el directorio padre de $relativePath
        $dir = dirname($relativePath);
        $this->info("Listing files under directory: {$dir}");
        $filesInDir = Storage::disk('s3')->files($dir);
        $this->info("Files found: " . print_r($filesInDir, true));

        // 3) Ahora sí, verificamos si existe $relativePath
        if (!Storage::disk('s3')->exists($relativePath)) {
            $this->error("Result file not found for Ticket #{$ticket->id}, Job Type: {$jobType} at {$relativePath}");
            return;
        }

        try {
            // ======================
            // CAMBIO IMPORTANTE
            // ======================
            
            // Descargamos el archivo .tar.gz de S3 a una ruta local relativa (disco "local").
            $fileContents = Storage::disk('s3')->get($relativePath);

            // Guardamos en: "temp/<jobId>.tar.gz" dentro de storage/app
            $localRelativePath = "temp/{$jobId}.tar.gz";
            Storage::disk('local')->put($localRelativePath, $fileContents);

            // Ahora sí generamos la ruta absoluta:
            $localTarFile = storage_path("app/{$localRelativePath}");
            
            // Extraer el archivo TAR.GZ
            $extractDir = storage_path("app/temp/{$jobId}");

            $this->extractTarGz($localTarFile, $extractDir);

            // Verificar el archivo JSON extraído
            $jsonFilePath = "{$extractDir}/output.json";
            if (!file_exists($jsonFilePath)) {
                $this->error("Extracted JSON file not found for Ticket #{$ticket->id}, Job Type: {$jobType}");
                return;
            }

            // Leer el contenido del archivo JSON
            $resultContent = file_get_contents($jsonFilePath);
            $results       = json_decode($resultContent, true);

            // Procesar los resultados según el tipo de job
            if ($jobType === 'classifier') {
                $this->updatePriority($ticket, $results);
            } elseif ($jobType === 'human intervention') {
                $this->updateNeedsHumanInteraction($ticket, $results);
            }

            $this->info("Results processed successfully for Ticket #{$ticket->id}, Job Type: {$jobType}");
        } catch (\Exception $e) {
            $this->error("Error processing results for Ticket #{$ticket->id}, Job Type: {$jobType}: " . $e->getMessage());
        }
    }

    protected function extractTarGz(string $tarFilePath, string $extractToDir)
    {
        if (!is_dir($extractToDir)) {
            mkdir($extractToDir, 0755, true);
        }

        // Primero extraemos el .gz a .tar
        $phar = new \PharData($tarFilePath);
        $phar->decompress(); // genera .tar
        $tarPath = str_replace('.gz', '', $tarFilePath);

        // Luego extraemos el .tar
        $pharTar = new \PharData($tarPath);
        $pharTar->extractTo($extractToDir);
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

    protected function updatePriority(Ticket $ticket, array $results)
    {
        $labels = $results['Labels'] ?? [];
        foreach ($labels as $label) {
            if (str_contains($label['Name'], 'Prioridad')) {
                $priorityValue    = (int) filter_var($label['Name'], FILTER_SANITIZE_NUMBER_INT);
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
        $classes      = $results['Classes'] ?? [];
        $highestScore = 0;
        $selectedClass = null;

        foreach ($classes as $class) {
            if ($class['Score'] > $highestScore) {
                $highestScore  = $class['Score'];
                $selectedClass = $class['Name'];
            }
        }

        if ($selectedClass !== null) {
            // Aquí interpretamos el valor
            $needsHumanInteraction = ($selectedClass === 'sí' || $selectedClass === 'Etiqueta') ? 1 : 0;
            $ticket->needsHumanInteraction = $needsHumanInteraction;
            $ticket->save();

            $this->info("Updated needsHumanInteraction for Ticket #{$ticket->id} to {$needsHumanInteraction}");
        } else {
            $this->warn("No valid class found in results for Ticket #{$ticket->id}");
        }
    }
}













