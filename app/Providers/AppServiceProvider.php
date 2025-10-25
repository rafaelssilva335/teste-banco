<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Domain\Repositories\AccountRepository::class, function ($app) {
            return new \App\Infra\Persistence\MemoryAccountRepository();
        });
        
        $this->app->singleton(\App\Domain\Repositories\TransactionRepository::class, function ($app) {
            return new \App\Infra\Persistence\MemoryTransactionRepository();
        });
        
        $this->app->singleton(\App\Domain\Services\BankService::class, function ($app) {
            return new \App\Domain\Services\BankService(
                $app->make(\App\Domain\Repositories\AccountRepository::class)
            );
        });
        
        $this->app->singleton(\App\Domain\Services\TransactionService::class, function ($app) {
            return new \App\Domain\Services\TransactionService(
                $app->make(\App\Domain\Repositories\TransactionRepository::class),
                $app->make(\App\Domain\Repositories\AccountRepository::class)
            );
        });
    }
}
