<?php

namespace App\Providers;

use Aws\Comprehend\ComprehendClient;
use Illuminate\Support\ServiceProvider;

class AwsComprehendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Register the AWS Comprehend service
        $this->app->singleton('AwsComprehend', function () {
            return new ComprehendClient([
                'region' => config('services.aws.region'),
                'version' => config('services.aws.version'),
                'credentials' => [
                    'key' => config('services.aws.key'),
                    'secret' => config('services.aws.secret'),
                ],
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}

