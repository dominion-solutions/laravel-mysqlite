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
        if($startTimeStamp != null && is_numeric($startTimeStamp) && $endTimeStamp != null && is_numeric($endTimeStamp)) {
            $differenceInt = $endTimeStamp - $startTimeStamp;
            if ($timeUnit == Constants::SECOND || $timeUnit = Constants::FRAC_SECOND) {
                return $differenceInt;
            }
            $difference = new DateTime();
            $difference->setTimestamp($differenceInt);
            return $difference->format("P$timeUnit");
        }
        return null;
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
        if ($timeExpression != null) {
            if (is_numeric($timeExpression)) {
                return $timeExpression;
            }
            $time = new DateTime($timeExpression);
            //Convert to the year zero according to Unix Timestamps.
            $time->setDate(1970, 1, 1);
            return $time->getTimestamp();
        }
        return null;
    }
}
