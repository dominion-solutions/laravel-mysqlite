# laravel-mysqlite
[![Build Status](https://travis-ci.org/spam-n-eggs/laravel-mysqlite.svg?branch=master)](https://travis-ci.org/spam-n-eggs/laravel-mysqlite)
[![Coverage Status](https://coveralls.io/repos/github/spam-n-eggs/laravel-mysqlite/badge.svg?branch=master)](https://coveralls.io/github/spam-n-eggs/laravel-mysqlite?branch=master)
[![StyleCI](https://github.styleci.io/repos/167069269/shield?branch=master)](https://github.styleci.io/repos/167069269)
[![Latest Stable Version](https://poser.pugx.org/spam-n-eggs/laravel-mysqlite/v/stable)](https://packagist.org/packages/spam-n-eggs/laravel-mysqlite)
[![Total Downloads](https://poser.pugx.org/spam-n-eggs/laravel-mysqlite/downloads)](https://packagist.org/packages/spam-n-eggs/laravel-mysqlite)
[![License](https://poser.pugx.org/spam-n-eggs/laravel-mysqlite/license)](https://packagist.org/packages/spam-n-eggs/laravel-mysqlite)

Laravel MySQLite is meant to be used in conjunction with Laravel.  It is a database connection that adds select functions from MySQL to SQLite.

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
- [convert_tz(date, fromTimezone, toTimezone)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_convert-tz)
- [date_format(date, format)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_date-format)
    - Un-ported Format Strings: `%U`, `%V`, `%X`
    - Other Limitations: `%j` is off by 1 day.
- [minute(time)](https://dev.mysql.com/doc/refman/5.7/en/date-and-time-functions.html#function_minute)
- [now()](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_now)
- [timestampdiff(timeUnit, startTimeStamp, endTimeStamp)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_timestampdiff)
- [time_to_sec(timeExpression)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_time-to-sec)
- [timediff(timeExpression1, timeExpression2)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_timediff)
- [to_days(date)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_to-days)
- [unix_timestamp(date = null)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_unix-timestamp)
- [utc_timestamp()][utc_timestamp]
- [weekday(date)](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_weekday)
### Flow
- [if(condition, onTrue, onFalse)](https://dev.mysql.com/doc/refman/8.0/en/control-flow-functions.html#function_if)
### Numeric
- [mod(number, divisor)](https://dev.mysql.com/doc/refman/5.7/en/mathematical-functions.html#function_mod)
  - Limitations - Support for Standard `MOD(N,M)` and `N % M` notation only.  `N MOD M` is not supported.
- [rand()](https://dev.mysql.com/doc/refman/8.0/en/mathematical-functions.html#function_rand)
- [sqrt(value)](https://dev.mysql.com/doc/refman/8.0/en/mathematical-functions.html#function_sqrt)
### String
- [concat(string ...)](https://dev.mysql.com/doc/refman/8.0/en/string-functions.html#function_concat)
- [concat_ws(separator, string ...)](https://dev.mysql.com/doc/refman/8.0/en/string-functions.html#function_concat-ws)
- [format(number, decimals, locale = 'en_US')](https://dev.mysql.com/doc/refman/8.0/en/string-functions.html#function_format)
- [lpad(string, length, pad)](https://dev.mysql.com/doc/refman/8.0/en/string-functions.html#function_lpad)
### Vectorface-Specific
#### Comparison
- [least(mixed ...)](https://github.com/Vectorface/MySQLite/blob/master/src/Vectorface/MySQLite/MySQL/Comparison.php)

# Contributing
Want to file a bug, contribute some code, improve documentation, or request a feature? Awesome Sauce! Read up on our guidelines for [contributing][contributing].  All contributions must follow our [Code of Conduct][codeofconduct].

# Questions
Have a question?  [Log an issue][issue] with the **Question** tag.  We'll get back to you in a timely fashion.

# Credits
This library uses other Open Source components. You can find the source code of their open source projects along with license information below. We acknowledge and are grateful to these developers for their contributions to open source community.

Project: Database https://github.com/illuminate/database
Copyright (c) Taylor Otwell
License (MIT) https://github.com/laravel/framework/blob/5.7/LICENSE.md

Project: MySQLite https://github.com/Vectorface/MySQLite
Copyright (c) 2014 Vectorface, Inc.
License: (MIT) https://github.com/Vectorface/MySQLite/blob/master/LICENSE

[utc_timestamp]: https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_utc-timestamp
[contributing]: ./.github/contributing.md
[issue]: https://github.com/spam-n-eggs/laravel-mysqlite/issues
[codeofconduct]:./.github/CODE_OF_CONDUCT.md
