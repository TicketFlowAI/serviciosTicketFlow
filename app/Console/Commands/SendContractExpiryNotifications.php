<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendContractExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-contract-expiry-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
    
        // Check for contracts expiring in specific timeframes
        $expiringContracts = ServiceContract::where(function ($query) use ($now) {
            $query->whereDate('end_date', $now->copy()->addDay())
                  ->orWhereDate('end_date', $now->copy()->addWeek())
                  ->orWhereDate('end_date', $now->copy()->addWeeks(2))
                  ->orWhereDate('end_date', $now->copy()->addMonth());
        })->get();
    
        foreach ($expiringContracts as $contract) {
            // Calculate days remaining until expiration
            $daysRemaining = $now->diffInDays($contract->end_date, false);
            
            // Set dynamic content based on days remaining
            if ($daysRemaining <= 1) {
                $viewTemplate = 'emails.services.expiring_soon';
                $subjectLine = "Urgent: Your " . $contract->service_name . " Service Expires Tomorrow";
            } elseif ($daysRemaining <= 7) {
                $viewTemplate = 'emails.services.expiring_week';
                $subjectLine = "Reminder: Your " . $contract->service_name . " Service Expires in 1 Week";
            } elseif ($daysRemaining <= 14) {
                $viewTemplate = 'emails.services.expiring_two_weeks';
                $subjectLine = "Friendly Reminder: Your " . $contract->service_name . " Service Expires in 2 Weeks";
            } else {
                $viewTemplate = 'emails.services.expiring_month';
                $subjectLine = "Advance Notice: Your " . $contract->service_name . " Service Expires in 1 Month";
            }
    
            // Prepare service data for the email
            $serviceData = [
                'company' => $contract->company_name,
                'serviceName' => $contract->service_name,
                'endDate' => $contract->end_date->format('Y-m-d'),
                'serviceType' => $contract->service_type,
            ];
    
            // Send the email
            Mail::to($contract->contact_email)->send(
                new ContractEndingNotification($serviceData, $viewTemplate, $subjectLine)
            );
    
            // Output to console (optional)
            $this->info("Notification sent to {$contract->contact_email} for contract ending on {$contract->end_date}");
        }
    }    
}
