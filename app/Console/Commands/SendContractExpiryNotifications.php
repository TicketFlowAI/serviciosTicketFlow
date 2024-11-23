<?php

namespace App\Console\Commands;

use App\Mail\ContractEndingNotification;
use App\Models\ServiceContract;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendContractExpiryNotifications extends Command
{
    protected $signature = 'app:send-contract-expiry-notifications';
    protected $description = 'Send contract expiry notifications to clients based on expiration date proximity';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();

        // Retrieve contracts and calculate expiration dates dynamically
        $expiringContracts = ServiceContract::with('serviceTerm','company','service')
            ->whereDoesntHave('serviceTerm', function ($query) {
                $query->where('months', 2); // Exclude monthly ServiceTerms
            })
            ->get()
            ->filter(function ($contract) use ($now) {
                // Calculate dynamic expiration date
                $expirationDate = $contract->created_at->copy()->addMonths($contract->serviceTerm->months);

                // Filter contracts based on expiration proximity
                return $expirationDate->isSameDay($now->copy()->addDay()) ||
                    $expirationDate->isSameDay($now->copy()->addWeek()) ||
                    $expirationDate->isSameDay($now->copy()->addWeeks(2)) ||
                    $expirationDate->isSameDay($now->copy()->addMonth());
            });

        foreach ($expiringContracts as $contract) {
            $expirationDate = $contract->created_at->copy()->addMonths($contract->serviceTerm->months);
            $daysRemaining = $now->diffInDays($expirationDate, false);

            // Determine the notification type based on days remaining
            if ($daysRemaining <= 1 && $contract->last_notification_type !== 'day') {
                $viewTemplate = 'emails.expiring_soon';
                $subjectLine = "Urgente, su servicio de " . $contract->service_name . " expira mañana!";
                $notificationType = 'day';
            } elseif ($daysRemaining <= 7 && $contract->last_notification_type !== 'week') {
                $viewTemplate = 'emails.expiring_week';
                $subjectLine = "Recordatorio, su servicio " . $contract->service_name . " expira en una semana";
                $notificationType = 'week';
            } elseif ($daysRemaining <= 14 && $contract->last_notification_type !== 'two_weeks') {
                $viewTemplate = 'emails.expiring_two_weeks';
                $subjectLine = "Recordatorio, su servicio " . $contract->service_name . " expira en 2 semanas";
                $notificationType = 'two_weeks';
            } elseif ($daysRemaining <= 30 && $contract->last_notification_type !== 'month') {
                $viewTemplate = 'emails.expiring_month';
                $subjectLine = "Su servicio " . $contract->service_name . " expira en 1 mes";
                $notificationType = 'month';
            } else {
                continue; // Skip if notification of this type was already sent
            }

            // Prepare service data for the email
            $serviceData = [
                'company' => $contract->company->name,
                'serviceName' => $contract->service->description,
                'endDate' => $expirationDate->format('Y-m-d'),
                'serviceType' => $contract->service_type,
            ];

            // Send the email
            Mail::to($contract->company->contactEmail)->send(
                new ContractEndingNotification($serviceData, $viewTemplate, $subjectLine)
            );

            // Update the last notification type sent
            $contract->last_notification_type = $notificationType;
            $contract->save();

            // Output to console (optional)
            $this->info("Notification sent to {$contract->company->contactEmail} for contract ending on {$expirationDate}");
        }
    }
}
