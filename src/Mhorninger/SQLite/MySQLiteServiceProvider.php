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
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return Connection connection
     */
    public function register()
    {
        Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new MySQLiteConnection($connection, $database, $prefix, $config);
        });
    }
}
