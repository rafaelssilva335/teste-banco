<?php

namespace Infrastructure\Providers;

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
        $this->app->singleton(\Core\Domain\Repositories\AccountRepository::class, function ($app) {
            return new \Infrastructure\Persistence\SqliteAccountRepository();
        });
        
        $this->app->singleton(\Core\Application\UseCases\DepositUseCase::class, function ($app) {
            return new \Core\Application\UseCases\DepositUseCase(
                $app->make(\Core\Domain\Repositories\AccountRepository::class)
            );
        });
        
        $this->app->singleton(\Core\Application\UseCases\WithdrawUseCase::class, function ($app) {
            return new \Core\Application\UseCases\WithdrawUseCase(
                $app->make(\Core\Domain\Repositories\AccountRepository::class)
            );
        });
        
        $this->app->singleton(\Core\Application\UseCases\TransferUseCase::class, function ($app) {
            return new \Core\Application\UseCases\TransferUseCase(
                $app->make(\Core\Domain\Repositories\AccountRepository::class)
            );
        });
        
        $this->app->singleton(\Core\Application\UseCases\GetBalanceUseCase::class, function ($app) {
            return new \Core\Application\UseCases\GetBalanceUseCase(
                $app->make(\Core\Domain\Repositories\AccountRepository::class)
            );
        });
        
        $this->app->singleton(\Core\Application\UseCases\ResetUseCase::class, function ($app) {
            return new \Core\Application\UseCases\ResetUseCase(
                $app->make(\Core\Domain\Repositories\AccountRepository::class)
            );
        });
        
        $this->app->singleton(\Core\Application\UseCases\GetAccountUseCase::class, function ($app) {
            return new \Core\Application\UseCases\GetAccountUseCase(
                $app->make(\Core\Domain\Repositories\AccountRepository::class)
            );
        });
    }
}
