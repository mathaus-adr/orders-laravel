<?php

namespace App\Providers;

use App\Infrastructure\Repositories\ClientRepository;
use App\Infrastructure\Repositories\OrderItemRepository;
use App\Infrastructure\Repositories\OrderRepository;
use Illuminate\Support\ServiceProvider;
use Orders\Domain\Interfaces\Repositories\ClientRepositoryInterface;
use Orders\Domain\Interfaces\Repositories\OrderItemRepositoryInterface;
use Orders\Domain\Interfaces\Repositories\OrderRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
