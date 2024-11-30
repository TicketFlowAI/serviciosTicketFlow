<?php

// app/Console/Commands/SendContractExpiryNotifications.php

namespace App\Console\Commands;

use App\Mail\ContractEndingNotification;
use App\Models\ServiceContract;
use App\Models\NotificationInterval;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendContractExpiryNotifications extends Command
{
    protected $signature = 'app:send-contract-expiry-notifications';
    protected $description = 'Send contract expiry notifications to clients based on expiration date proximity';

    public function handle()
    {
        $now = Carbon::now();

        // Retrieve notification intervals from the database
        $intervals = NotificationInterval::all();

        // Retrieve contracts and calculate expiration dates dynamically
        $expiringContracts = ServiceContract::with('serviceTerm', 'company', 'service')
            ->whereDoesntHave('serviceTerm', function ($query) {
                $query->where('months', 2); // Exclude monthly ServiceTerms
            })
            ->get();

        foreach ($expiringContracts as $contract) {
            $expirationDate = $contract->created_at->copy()->addMonths($contract->serviceTerm->months);
            $daysRemaining = $now->diffInDays($expirationDate, false);

            foreach ($intervals as $interval) {
                if ($daysRemaining <= $interval->days && $contract->last_notification_type !== $interval->type) {
                    $template = $interval->template_name;
                    $subjectLine = str_replace(['{service}', '{days}'], [$contract->service->description, $interval->days], $interval->subject_template);
                    $notificationType = $interval->type;

                    // Prepare service data for the email
                    $serviceData = [
                        'company' => $contract->company->name,
                        'serviceName' => $contract->service->description,
                        'endDate' => $expirationDate->format('d-m-y'),
                        'serviceType' => $contract->service_type,
                    ];

                    // Send the email
                    Mail::to($contract->company->contactEmail)->send(
                        new ContractEndingNotification($serviceData, $template, $subjectLine)
                    );

                    // Update the last notification type sent
                    $contract->last_notification_type = $notificationType;
                    $contract->save();

                    // Output to console (optional)
                    $this->info("Notification sent to {$contract->company->contactEmail} for contract ending on {$expirationDate}");
                    break; // Break after sending the appropriate notification
                }
            }
        }
    }
}