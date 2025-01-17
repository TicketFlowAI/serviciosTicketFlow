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
            // Descargar archivo de S3
            $fileContents = Storage::disk('s3')->get($relativePath);

            // Ruta base para archivos temporales
            $baseTempPath = '/home/servicios/htdocs/servicios.mindsoftdev.com/storage/app/private/temp';
            $localDir = "{$baseTempPath}/{$jobId}";

            // Crear directorio local para el Job ID
            if (!file_exists($localDir)) {
                mkdir($localDir, 0775, true);
                $this->info("Directory created: {$localDir}");
            } else {
                $this->info("Directory already exists: {$localDir}");
            }

            // Verificar existencia del directorio
            if (!file_exists($localDir)) {
                $this->error("Directory does not exist after creation: {$localDir}");
                return;
            }

            // Guardar el archivo descargado localmente
            $localTarGzPath = "{$localDir}/output.tar.gz";
            file_put_contents($localTarGzPath, $fileContents);

            $this->info("File downloaded and stored locally at: {$localTarGzPath}");

            // Extraer los archivos
            $this->extractTarGz($localTarGzPath, $localDir);

            // Procesar el archivo JSON
            $jsonFilePath = "{$localDir}/output.json";
            if (!file_exists($jsonFilePath)) {
                $this->error("Extracted JSON file not found for Ticket #{$ticket->id}, Job Type: {$jobType}.");
                return;
            }

            $resultContent = file_get_contents($jsonFilePath);
            $results = json_decode($resultContent, true);

            if ($jobType === 'classifier') {
                $this->updatePriority($ticket, $results);
            } elseif ($jobType === 'human intervention') {
                $this->updateNeedsHumanInteraction($ticket, $results);
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
            $phar = new \PharData($tarFilePath);
            $phar->decompress(); // Crea un archivo .tar

            $tarPath = str_replace('.gz', '', $tarFilePath);

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
}





















