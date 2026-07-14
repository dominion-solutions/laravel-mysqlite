<?php

namespace Mhorninger\MySQLite\MySQL;

trait Miscellaneous
{
    //phpcs:disable
    public static function mysql_inet_ntoa(int|string|null $numeric): ?string
    {
        //phpcs:enable
        if ($numeric && is_numeric($numeric)) {
            return long2ip((int) sprintf('%d', $numeric));
        }

        return null;
    }
}
