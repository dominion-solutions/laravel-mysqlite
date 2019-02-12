<?php

namespace Mhorninger\MySQLite\MySQL;

use DateTime;
use Mhorninger\MySQLite\Constants;

trait NumericExtended
{
    // phpcs:disable
    public static function mysql_mod($number, $divisor)
    {
        //phpcs:enable
        if ($number && $divisor) {
            return fmod($number, $divisor);
        }
        return null;
    }
}
