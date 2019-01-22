<?php
namespace Mhorninger\SQLite;

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
        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);
    }
    /**
     * Register the service provider.
     * 
     * @return Connection connection
     */
    public function register()
    {
        // Add database driver.
        $this->app->resolving(
            'db',
            function ($db) {
                $db->extend(
                    'sqlite',
                    function ($config, $name) {
                        $config['name'] = $name;
                        return new Connection($config);
                    }
                );
            }
        );
    }   
}
