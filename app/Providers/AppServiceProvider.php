<?php

namespace App\Providers;

use App\Interfaces\courseRepositoryInterface;
use App\Interfaces\studentRepositoryInterface;
use App\Interfaces\teacherRepositoryInterface;
use App\Interfaces\userRepositoryInterface;
use App\Repositories\courseRepository;
use App\Repositories\studentRepository;
use App\Repositories\teacherRepository;
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
        $this->app->bind(teacherRepositoryInterface::class, teacherRepository::class);
        $this->app->bind(studentRepositoryInterface::class, studentRepository::class);
        // Bind custom services
        $this->app->bind(Neo4jUserProvider::class, function ($app) {
            return new Neo4jUserProvider();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Neo4jUserProvider for authentication
        Auth::provider('neo4j', function ($app, array $config) {
            return new Neo4jUserProvider();
        });
    }
}
