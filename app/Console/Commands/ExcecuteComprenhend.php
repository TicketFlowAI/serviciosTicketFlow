<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Aws\Comprehend\ComprehendClient;
use Illuminate\Support\Facades\Storage;

class ExcecuteComprenhend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:excecute-comprenhend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a classification job on Amazon Comprehend';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Amazon Comprehend job process...');

        // Step 1: Start jobs for pending tickets
        $this->startJobs();

        // Step 2: Check the status of ongoing jobs
        $this->checkJobStatus();

        $this->info('Amazon Comprehend job process completed.');
    }

    /**
     * Start jobs for tickets in "pending" status without job_id.
     */
    protected function startJobs()
    {
        $tickets = Ticket::where('status', 'pending')
                         ->whereNull('job_id')
                         ->get();

        if ($tickets->isEmpty()) {
            $this->info('No pending tickets to process.');
            return;
        }

        $comprehendClient = new ComprehendClient([
            'region' => config('services.aws.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
            ],
        ]);

        foreach ($tickets as $ticket) {
            try {
                // Define S3 Input/Output URIs
                $inputUri = 's3://comprenhend-dataset/' . $ticket->id . '.csv';
                $outputUri = 's3://comprenhend-dataset/output/';

                // Start the Comprehend classification job
                $response = $comprehendClient->startDocumentClassificationJob([
                    'JobName' => 'TicketJob-' . $ticket->id,
                    'DocumentClassifierArn' => 'arn:aws:comprehend:us-east-2:123456789012:document-classifier/PriorityClassifier/version/v1',
                    'InputDataConfig' => [
                        'S3Uri' => $inputUri,
                        'InputFormat' => 'ONE_DOC_PER_LINE',
                    ],
                    'OutputDataConfig' => [
                        'S3Uri' => $outputUri,
                    ],
                    'DataAccessRoleArn' => 'arn:aws:iam::123456789012:role/AmazonComprehendServiceRole-AmazonComprehendServiceRole',
                ]);

                $jobId = $response['JobId'];
                $ticket->update(['job_id' => $jobId]);

                $this->info("Job started for Ticket #{$ticket->id} with Job ID: {$jobId}");
            } catch (\Exception $e) {
                $this->error("Error processing Ticket #{$ticket->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Check the status of jobs in progress.
     */
    protected function checkJobStatus()
    {
        $tickets = Ticket::where('status', 'pending')
                         ->whereNotNull('job_id')
                         ->get();

        if ($tickets->isEmpty()) {
            $this->info('No jobs to check.');
            return;
        }

        $comprehendClient = new ComprehendClient([
            'region' => config('services.aws.region'),
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
                } elseif ($status === 'FAILED') {
                    $ticket->update(['status' => 'failed']);
                    $this->error("Job failed for Ticket #{$ticket->id}");
                }
            } catch (\Exception $e) {
                $this->error("Error checking status for Ticket #{$ticket->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Process results from completed jobs.
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

        $ticket->update([
            'status' => 'processed',
            'classification_results' => $results,
        ]);

        $this->info("Results processed for Ticket #{$ticket->id}");
    }
}

