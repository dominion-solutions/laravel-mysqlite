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

    //phpcs:disable
    public static function mysql_time_to_sec($timeExpression)
    {
        //phpcs:enable
        $time = new DateTime($timeExpression);
        //Convert to the year zero according to Unix Timestamps.
        $time->setDate(1970, 1, 1);
        return $time->getTimestamp();
    }
}
