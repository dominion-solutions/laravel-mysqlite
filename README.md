# laravel-mysqlite
[![Build Status](https://travis-ci.org/spam-n-eggs/laravel-mysqlite.svg?branch=master)](https://travis-ci.org/spam-n-eggs/laravel-mysqlite)
[![Coverage Status](https://coveralls.io/repos/github/spam-n-eggs/laravel-mysqlite/badge.svg?branch=master)](https://coveralls.io/github/spam-n-eggs/laravel-mysqlite?branch=master)

Laravel MySQLite is meant to be used in conjunction with Laravel.  It is a wrapper class that adds select functions from MySQL to SQLite.  See [Vectorface/MySQLite](https://github.com/Vectorface/MySQLite) for more details around what is included by default.

# Usage
## Adding the Composer Resource
1. Execute `composer require spam-n-eggs/laravel-mysqlite` or alternatively `composer require --dev spam-n-eggs/laravel-mysqlite`

## Registering as a Service Provider
In order to provide clutter it is preferable to create a separate Service Provider 
1. If there is a need to conditionally register the Service (i.e. you only use it in testing) create a new class in `app/Providers` that extends `Mhorninger\SQLite\MySQLiteServiceProvider`

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
# Ported Functionality
## Constants
- [UTC_TIMESTAMP][utc_timestamp]
## Methods
### Aggregate
- [bit_or (int ...)](https://dev.mysql.com/doc/refman/8.0/en/group-by-functions.html#function_bit-or)
### Date and Time
- [now()](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_now)
- [timestampdiff($timeUnit, $startTimeStamp, $endTimeStamp)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_timestampdiff)
- [time_to_sec($timeExpression)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_time-to-sec)
- [to_days($date)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_to-days)
- [unix_timestamp($date = null)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_unix-timestamp)
- [utc_timestamp()][utc_timestamp]
### Flow
- [if($condition, $onTrue, $onFalse)](https://dev.mysql.com/doc/refman/8.0/en/control-flow-functions.html#function_if)
### Numeric
- [rand()](https://dev.mysql.com/doc/refman/8.0/en/mathematical-functions.html#function_rand)
- [sqrt($value)](https://dev.mysql.com/doc/refman/8.0/en/mathematical-functions.html#function_sqrt)
### String
- [concat(string ...)](https://dev.mysql.com/doc/refman/8.0/en/string-functions.html#function_concat)
- [concat_ws(separator, string ...)](https://dev.mysql.com/doc/refman/8.0/en/string-functions.html#function_concat-ws)
### Vectorface-Specific
#### Comparison
- [least(mixed ...)](https://github.com/Vectorface/MySQLite/blob/master/src/Vectorface/MySQLite/MySQL/Comparison.php)

[utc_timestamp]: https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_utc-timestam
