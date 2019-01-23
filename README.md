# laravel-mysqlite
[![Build Status](https://travis-ci.org/spam-n-eggs/laravel-mysqlite.svg?branch=master)](https://travis-ci.org/spam-n-eggs/laravel-mysqlite)

Laravel MySQLite is meant to be used in conjunction with Laravel.  It is a wrapper class that adds select functions from MySQL to SQLite.  See [Vectorface/MySQLite](https://github.com/Vectorface/MySQLite) for more details around what is included by default.
# Usage
## Adding the Composer Resource
1. In your composer.json add the following line to the repositories section:
    ```json
    {
      "type": "vcs",
      "url": "https://github.com/spam-n-eggs/laravel-mysqlite.git"
    }
    ```
1. Execute `composer require spam-n-eggs/laravel-mysqlite`

## Registering as a Service Provider
In order to provide clutter it is preferable to create a separate Service Provider 
1. Create a new class in `app/Providers` that extends `Mhorninger\SQLite\MySQLiteServiceProvider`

    ```php
    <?php
    namespace App\Providers;

    use Mhorninger\SQLite\MySQLiteServiceProvider as ServiceProvider;

    class MySQLiteServiceProvider extends ServiceProvider
    {
        public function register()
        {
            if ($shouldRegister) {
                parent::register();
            }
        }
    }
    ```
1. Add a line to `app/Providers/AppServiceProvider.php` within the `register()` method:
    ```php
    $this->app->register(MySQLiteServiceProvider::class);
    ```
