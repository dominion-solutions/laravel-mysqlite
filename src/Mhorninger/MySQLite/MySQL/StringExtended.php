<?php

namespace Mhorninger\MySQLite\MySQL;

use DateTime;
use Mhorninger\MySQLite\Constants;

trait StringExtended
{
    // phpcs:disable
    public static function mysql_lpad($string, $length, $pad)
    {
        //phpcs:enable
        if ($string && $length && $pad) {
            if (strlen($string) < $length) {
                return str_pad($string, $length, $pad, STR_PAD_LEFT);
            }
            return substr($string, 0, $length);
        }
        return null;
    }
}
