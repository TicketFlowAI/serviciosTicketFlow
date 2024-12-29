<?php

namespace App\Providers;

use App\Interfaces\{
    CompanyRepositoryInterface,
    CategoryRepositoryInterface,
    EmailRepositoryInterface,
    IntervalRepositoryInterface,
    MessageRepositoryInterface,
    RoleRepositoryInterface,
    ServiceContractRepositoryInterface,
    ServiceRepositoryInterface,
    ServiceTermRepositoryInterface,
    TaxRepositoryInterface,
    TicketRepositoryInterface,
    UserRepositoryInterface
};

use App\Repositories\{
    CategoryRepository,
    CompanyRepository,
    EmailRepository,
    IntervalRepository,
    MessageRepository,
    RoleRepository,
    ServiceContractRepository,
    ServiceRepository,
    ServiceTermRepository,
    TaxRepository,
    TicketRepository,
    UserRepository
};
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
            RoleRepositoryInterface::class => RoleRepository::class,
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
