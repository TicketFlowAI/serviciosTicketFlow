<?php

namespace App\Providers;

use App\Interfaces\CompanyRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\EmailRepositoryInterface;
use App\Interfaces\IntervalRepositoryInterface;
use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\ServiceContractRepositoryInterface;
use App\Interfaces\ServiceRepositoryInterface;
use App\Interfaces\ServiceTermRepositoryInterface;
use App\Interfaces\TaxRepositoryInterface;
use App\Interfaces\TicketRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\EmailRepository;
use App\Repositories\IntervalRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ServiceContractRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceTermRepository;
use App\Repositories\TaxRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
/**
     * Register services.
     */
    public function register(): void
    {
        $repositories = [
            CompanyRepositoryInterface::class => CompanyRepository::class,
            ServiceRepositoryInterface::class => ServiceRepository::class,
            TaxRepositoryInterface::class => TaxRepository::class,
            CategoryRepositoryInterface::class => CategoryRepository::class,
            ServiceTermRepositoryInterface::class => ServiceTermRepository::class,
            ServiceContractRepositoryInterface::class => ServiceContractRepository::class,
            TicketRepositoryInterface::class => TicketRepository::class,
            MessageRepositoryInterface::class => MessageRepository::class,
            UserRepositoryInterface::class => UserRepository::class,
            EmailRepositoryInterface::class => EmailRepository::class,
            IntervalRepositoryInterface::class => IntervalRepository::class,
        ];
    
        foreach ($repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
