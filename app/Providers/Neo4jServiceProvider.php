<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laudis\Neo4j\DriverFactory;
use Laudis\Neo4j\Authentication\Authenticate;

class Neo4jServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Explicitly define the Neo4j connection name
        $this->app->singleton('neo4j', function ($app) {
            // Fetch Neo4j credentials from config/database.php
            $neo4jUri = config('database.connections.neo4j.uri');
            $neo4jUsername = config('database.connections.neo4j.username');
            $neo4jPassword = config('database.connections.neo4j.password');

            $auth = Authenticate::basic($neo4jUsername, $neo4jPassword);
            return DriverFactory::create($neo4jUri, null, $auth);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
