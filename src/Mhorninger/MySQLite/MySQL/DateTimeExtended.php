<?php

namespace Mhorninger\MySQLite\MySQL;

use DateTime;
use DateTimeZone;
use Mhorninger\MySQLite\SubstitutionConstants;

trait DateTimeExtended
{
    //phpcs:disable
    public static function mysql_convert_tz($date, $fromTimezone, $toTimezone)
    {
        //phpcs:enable
        if ($date && $fromTimezone && $toTimezone) {
            $toTimezone = new DateTimeZone($toTimezone);
            if ($fromTimezone == 'SYSTEM') {
                $converted = new DateTime($date);
            } else {
                $fromTimezone = new DateTimeZone($fromTimezone);
                $converted = new DateTime($date, $fromTimezone);
            }
            $converted->setTimezone($toTimezone);

            return $converted->format('Y-m-d H:i:s');
        }
    }

    //phpcs:disable
    public static function mysql_date_format($date, $format)
    {
        //phpcs:enable
        $dictionary = [
            '%a' => 'D',
            '%b' => 'M',
            '%c' => 'n',
            '%D' => 'jS',
            '%d' => 'd',
            '%e' => 'j',
            '%f' => 'u',
            '%H' => 'H',
            '%h' => 'h',
            '%I' => 'h',
            '%i' => 'i',
            '%j' => 'z',
            '%k' => 'G',
            '%l' => 'g',
            '%M' => 'F',
            '%m' => 'm',
            '%p' => 'A',
            '%r' => 'h:i:s A',
            '%S' => 's',
            '%s' => 's',
            '%T' => 'H:i:s',
            '%u' => 'W',
            '%v' => 'W',
            '%W' => 'l',
            '%w' => 'w',
            '%x' => 'o',
            '%Y' => 'Y',
            '%y' => 'y',
            '%%' => '%',
        ];

        if ($date && $format) {
            $time = new DateTime($date);
            $keys = array_keys($dictionary);
            foreach ($keys as $key) {
                $format = str_replace($key, $dictionary[$key], $format);
            }

            return $time->format($format);
        }
    }

    // phpcs:disable
    public static function mysql_minute($time)
    {
        // phpcs:enable
        if ($time) {
            $asTime = new DateTime($time);

            return date_format($asTime, 'i');
        }
    }

    // phpcs:disable
    public static function mysql_timediff($timeExpression1, $timeExpression2)
    {
        // phpcs:enable
        if ($timeExpression1 && $timeExpression2) {
            $dateTime1 = new DateTime($timeExpression1);
            $dateTime2 = new DateTime($timeExpression2);
            $dateTimeInterval = $dateTime2->diff($dateTime1);
            $days = $dateTimeInterval->d;
            $hours = ($days * 24) + $dateTimeInterval->h;
            $hourFormatter = new \NumberFormatter(\Locale::DEFAULT_LOCALE, \NumberFormatter::PATTERN_DECIMAL, '00');
            $hours = $hourFormatter->format($hours, \NumberFormatter::PATTERN_DECIMAL);

            return $dateTimeInterval->format("%r$hours:%I:%S.%F");
        }
    }

    // phpcs:disable
    public static function mysql_timestampdiff($timeUnit, $startTimeStamp, $endTimeStamp)
    {
        //phpcs:enable
        if ($startTimeStamp != null && is_numeric($startTimeStamp) && $endTimeStamp != null && is_numeric($endTimeStamp)) {
            $differenceInt = $endTimeStamp - $startTimeStamp;
            if ($timeUnit == SubstitutionConstants::SECOND || $timeUnit = SubstitutionConstants::FRAC_SECOND) {
                return $differenceInt;
            }
            $difference = new DateTime();
            $difference->setTimestamp($differenceInt);

            return $difference->format("P$timeUnit");
        }
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
    }

    //phpcs:disable
    public static function mysql_utc_timestamp()
    {
        //phpcs:enable
        $now = new DateTime();

        return $now->getTimestamp();
    }

    // phpcs:disable
    public static function mysql_weekday($date)
    {
        if ($date) {
            $dateTime = new DateTime($date);
            return $dateTime->format('N') - 1;
        }
    }
}
