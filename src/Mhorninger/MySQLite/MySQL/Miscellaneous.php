<?php

namespace Mhorninger\MySQLite\MySQL;

trait Miscellaneous
{
    //phpcs:disable
    public static function mysql_inet_ntoa($numeric)
    {
        //phpcs:enable
        if ($numeric && is_numeric($numeric)) {
            return long2ip(sprintf('%d', $numeric));
        }
    }
}
