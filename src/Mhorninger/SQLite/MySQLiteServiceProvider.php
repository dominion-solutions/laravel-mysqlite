<?php

namespace Mhorninger\SQLite;

use Illuminate\Database\Connection;

/**
 * The Laravel ServiceProvider that initializes the MySQLite Connection.
 */
class MySQLiteServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new MySQLiteConnection($connection, $database, $prefix, $config);
        });
    }
}
