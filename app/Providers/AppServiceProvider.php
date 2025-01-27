<?php

namespace App\Providers;

use App\Interfaces\courseRepositoryInterface;
use App\Interfaces\userRepositoryInterface;
use App\Repositories\courseRepository;
use App\Repositories\userRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(userRepositoryInterface::class, userRepository::class);
        $this->app->bind(courseRepositoryInterface::class, courseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::provider('neo4j', function ($app, array $config) {
            return new Neo4jUserProvider();
        });
    }
}
