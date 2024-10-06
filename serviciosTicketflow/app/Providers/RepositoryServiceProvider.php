<?php

namespace App\Providers;

use App\Interfaces\CompanyRepositoryInterface;
use App\Interfaces\Interfaces\ServiceRepositoryInterface;
use App\Interfaces\Interfaces\UserRepositoryInterface;
use App\Repositories\CompanyRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
/**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CompanyRepositoryInterface::class,CompanyRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class,ServiceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
