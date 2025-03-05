<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Repositories\UrlRepository\UrlRepository;
use App\Repositories\UrlRepository\UrlRepositoryInterface;
use App\Services\UrlService\UrlService;
use App\Services\UrlService\UrlServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Singleton for Repository
        $this->app->singleton(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->singleton(UrlRepositoryInterface::class, UrlRepository::class);

        // Singleton for Service
        $this->app->singleton(UrlServiceInterface::class, UrlService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
