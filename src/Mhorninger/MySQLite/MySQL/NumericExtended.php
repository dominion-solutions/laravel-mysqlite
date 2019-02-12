<?php

namespace Mhorninger\MySQLite\MySQL;

trait NumericExtended
{
    // phpcs:disable
    public static function mysql_mod($number, $divisor)
    {
        //phpcs:enable
        if ($number && $divisor) {
            return fmod($number, $divisor);
        }
    }
}
