<?php

namespace App\Providers;

use App\Interfaces\CompanyRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\ServiceContractRepositoryInterface;
use App\Interfaces\ServiceRepositoryInterface;
use App\Interfaces\ServiceTermRepositoryInterface;
use App\Interfaces\TaxRepositoryInterface;
use App\Interfaces\TicketRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ServiceContractRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceTermRepository;
use App\Repositories\TicketRepository;
use App\TaxRepository;
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
        $this->app->bind(TaxRepositoryInterface::class,TaxRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class,CategoryRepository::class);
        $this->app->bind(ServiceTermRepositoryInterface::class,ServiceTermRepository::class);
        $this->app->bind(ServiceContractRepositoryInterface::class,ServiceContractRepository::class);
        $this->app->bind(TicketRepositoryInterface::class,TicketRepository::class);
        $this->app->bind(MessageRepositoryInterface::class,MessageRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
