<?php

namespace Mhorninger\MySQLite\MySQL;

use DateTime;
use Mhorninger\MySQLite\Constants;

trait DateTimeExtended
{
    // phpcs:disable
    public static function mysql_timestampdiff($timeUnit, $startTimeStamp, $endTimeStamp)
    {
        //phpcs:enable
        $differenceInt = $endTimeStamp - $startTimeStamp;
        if ($timeUnit == Constants::SECOND || $timeUnit = Constants::FRAC_SECOND) {
            return $differenceInt;
        }
        $difference = new DateTime();
        $difference->setTimestamp($differenceInt);
        return $difference->format("P$timeUnit");
    }

    //phpcs:disable
    public static function mysql_utc_timestamp()
    {
        //phpcs:enable
        $now = new DateTime();
        return $now->getTimestamp();
    }
}
