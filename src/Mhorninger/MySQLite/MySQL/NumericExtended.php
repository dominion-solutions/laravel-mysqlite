<?php

namespace Mhorninger\MySQLite\MySQL;

trait NumericExtended
{
    // phpcs:disable
    public static function mysql_mod(float|int|null $number, float|int|null $divisor): ?float
    {
        //phpcs:enable
        if ($number && $divisor) {
            return fmod($number, $divisor);
        }

        return null;
    }
}
