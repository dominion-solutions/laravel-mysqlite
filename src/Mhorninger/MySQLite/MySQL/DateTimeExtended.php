<?php

namespace Mhorninger\MySQLite\MySQL;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
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
            //SYSTEM is a reserved timezone in MySQL.  date_default_timezone_get is a good workaround.
            if ($fromTimezone == 'SYSTEM') {
                $fromTimezone = date_default_timezone_get();
            }
            if ($toTimezone == 'SYSTEM') {
                $toTimezone = date_default_timezone_get();
            }

            $fromTimezone = new DateTimeZone($fromTimezone);
            $toTimezone = new DateTimeZone($toTimezone);
            $converted = new DateTime($date, $fromTimezone);

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
    public static function mysql_hour($time)
    {
        if ($time) {
            $asTime = new DateTime($time);

            return $asTime->format('G');
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

    //phpcs:disable
    public static function mysql_week($date, $mode)
    {
        if ($date) {
            $date = CarbonImmutable::parse($date);
            $first = $date->firstOfYear();
            $last = $date->lastOfYear();
            $lastYearLeapYear = ($date->year - 1) % 4;

            switch ($mode) {
                case 0: //starts on sunday, first week is first sunday and goes 0-53
                    $sow = $date->startOfWeek(Carbon::SUNDAY);
                    $eow = $date->endOfWeek(Carbon::SATURDAY);
                    $firstSunday = $date->firstOfYear(Carbon::SUNDAY);

                    if ($sow->year < $date->year) {
                        if ($date->month == 12 && $date->isoWeek == 1) {
                            return '53';
                        } else {
                            return '0';
                        }
                    } elseif ($eow->year > $date->year) {
                        if ($last->dayOfWeek > 1 || $last->dayOfWeek == 1 && ($date->year % 4) != 0) {
                            return '52';
                        } else {
                            return '53';
                        }
                    } elseif ($firstSunday == $first) {
                        return $date->isoWeek(null, Carbon::SUNDAY);
                    } else {
                        if ($firstSunday->weekOfYear == 1) {
                            return $date->weekOfYear - 1;
                        } else {
                            return $date->weekOfYear;
                        }
                    }
                    break;
                case 1: //starts on monday, first week is one with 4 or more days and goes 0-53
                    if ($date->month == 1 && $date->isoWeek >= 52) { //checks if it is rounding a week of the next year back to 53 and changes it to allows 0 week
                        return '0';
                    } elseif ($date->month == 12 && $date->isoWeek == 1) { //check if it is rounging a week form the previous year to 1
                        return '53';
                    } else {
                        return $date->isoWeek();
                    }
                    break;
                case 2: //starts on sunday, first week is first sunday and goes 1-53
                    $sow = $date->startOfWeek(Carbon::SUNDAY);
                    $eow = $date->endOfWeek(Carbon::SATURDAY);
                    $firstSunday = $date->firstOfYear(Carbon::SUNDAY);

                    if ($sow->year < $date->year) {
                        if ($first->dayOfWeek < 2 || ($first->dayOfWeek == 2 && $lastYearLeapYear == 0)) {
                            return '53';
                        } else {
                            return '52';
                        }
                    } elseif ($eow->year > $date->year) {
                        if ($last->dayOfWeek > 1 || $last->dayOfWeek == 1 && ($date->year % 4) != 0) {
                            return '52';
                        } else {
                            return '53';
                        }
                    } elseif ($firstSunday == $first) {
                        return $date->isoWeek(null, Carbon::SUNDAY);
                    } else {
                        if ($firstSunday->weekOfYear == 1) {
                            return $date->weekOfYear - 1;
                        } else {
                            return $date->weekOfYear;
                        }
                    }
                    break;
                case 3: //starts on monday, first week is one with 4 or more days and goes 1-53 (same as base algorithm)
                    return $date->isoWeek;
                    break;
                case 4: //starts on sunday, first week is one with 4 or more days and goes 0-53
                    if ($date->month == 1 && $date->isoWeek(null, Carbon::SUNDAY) >= 52) { //checks if it is rounding a week of the next year back to 53 and changes it to allows 0 week
                        return '0';
                    } elseif ($date->month == 12 && $date->isoWeek(null, Carbon::SUNDAY) == 1) {
                        return '53';
                    } else {
                        return $date->isoWeek(null, Carbon::SUNDAY);
                    }
                    break;
                case 5: //starts on monday, first week is first monday and goes 0-53
                    $sow = $date->startOfWeek(Carbon::MONDAY);
                    $eow = $date->endOfWeek(Carbon::SUNDAY);
                    $firstMonday = $date->firstOfYear(Carbon::MONDAY);

                    if ($sow->year < $date->year) {
                        if ($date->month == 12 && $date->isoWeek == 1) { //check if it is rounging a week form the previous year to 1
                            return '53';
                        } else {
                            return '0';
                        }
                    } elseif ($eow->year > $date->year) {
                        if ($last->dayOfWeekIso > 2 || $last->dayOfWeekIso == 2 && ($date->year % 4) != 0) {
                            return '52';
                        } else {
                            return '53';
                        }
                    } elseif ($firstMonday == $first) {
                        return $date->weekOfYear;
                    } else {
                        if ($firstMonday->weekOfYear == 1) {
                            return $date->weekOfYear;
                        } else {
                            return $date->weekOfYear - 1;
                        }
                    }
                    break;
                case 6: //starts on sunday, first week is one with 4 or more days and goes 1-53
                    return $date->isoWeek(null, Carbon::SUNDAY);
                    break;
                case 7: //starts on monday, first week is first monday and goes 1-53
                    $sow = $date->startOfWeek(Carbon::MONDAY);
                    $eow = $date->endOfWeek(Carbon::SUNDAY);
                    $firstMonday = $date->firstOfYear(Carbon::MONDAY);

                    if ($sow->year < $date->year) { //if the year of the monday at the start of the given week (sow) is the year before the year of the actual date given
                        if ($first->dayOfWeekIso < 3 || ($first->dayOfWeekIso == 3 && $lastYearLeapYear == 0)) { //if the first day of the year (first) is tuesday or monday OR if the first day is wendsday and the last year is a leap year
                            return '53';
                        } else {
                            return '52';
                        }
                    } elseif ($eow->year > $date->year) { //if the year of the sunday at the end of the given week (eow) is the year after the year of the actual date given
                        if ($last->dayOfWeekIso > 2 || $last->dayOfWeekIso == 2 && ($date->year % 4) != 0) { //if the last day of the year is (last) is wendsday, thursday, friday, saturday or sunday OR if the last day is tuesday and the current year is a leap year
                            return '52';
                        } else {
                            return '53';
                        }
                    } elseif ($firstMonday == $first) { //if the first monday of the year is the same as January 1st of the given year
                        return $date->weekOfYear;
                    } else {
                        if ($firstMonday->weekOfYear == 1) { //if the week with the first monday on is the first week, then it matches what we want
                            return $date->weekOfYear;
                        } else { //if the week with the first monday is the second week then we need to subtract a week because it counted the first week as the week before because it have 4 days in it
                            return $date->weekOfYear - 1;
                        }
                    }
                    break;
                default: //same as case 0
                    $sow = $date->startOfWeek(Carbon::SUNDAY);
                    $eow = $date->endOfWeek(Carbon::SATURDAY);
                    $firstSunday = $date->firstOfYear(Carbon::SUNDAY);

                    if ($sow->year < $date->year) {
                        if ($date->month == 12 && $date->isoWeek == 1) {
                            return '53';
                        } else {
                            return '0';
                        }
                    } elseif ($eow->year > $date->year) {
                        if ($last->dayOfWeek > 1 || $last->dayOfWeek == 1 && ($date->year % 4) != 0) {
                            return '52';
                        } else {
                            return '53';
                        }
                    } elseif ($firstSunday == $first) {
                        return $date->isoWeek(null, Carbon::SUNDAY);
                    } else {
                        if ($firstSunday->weekOfYear == 1) {
                            return $date->weekOfYear - 1;
                        } else {
                            return $date->weekOfYear;
                        }
                    }
                    break;
            }
        }
    }

    //phpcs:disable
    public static function mysql_yearweek($date, $mode)
    {
        if ($date) {
            $date = CarbonImmutable::parse($date);
            $first = $date->firstOfYear();
            $last = $date->lastOfYear();
            $lastYearLeapYear = ($date->year - 1) % 4;

            switch ($mode) {
                case 0: //uses same algorithm as mode 2 of WEEK function
                    $sow = $date->startOfWeek(Carbon::SUNDAY);
                    $eow = $date->endOfWeek(Carbon::SATURDAY);
                    $firstSunday = $date->firstOfYear(Carbon::SUNDAY);

                    if ($sow->year < $date->year) {
                        if ($first->dayOfWeek < 2 || ($first->dayOfWeek == 2 && $lastYearLeapYear == 0)) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($eow->year > $date->year) {
                        if ($last->dayOfWeek > 1 || $last->dayOfWeek == 1 && ($date->year % 4) != 0) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($firstSunday == $first) {
                        return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->isoWeek(null, Carbon::SUNDAY), '2', '0', STR_PAD_LEFT);
                    } else {
                        if ($firstSunday->weekOfYear == 1) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad(($date->weekOfYear - 1), '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->weekOfYear, '2', '0', STR_PAD_LEFT);
                        }
                    }
                    break;
                case 1: //uses same algorithm as mode 3 of WEEK function
                    return $date->isoWeekYear().str_pad($date->isoWeek, '2', '0', STR_PAD_LEFT);
                    break;
                case 2: //uses same algorithm as mode 2 of WEEK function
                    $sow = $date->startOfWeek(Carbon::SUNDAY);
                    $eow = $date->endOfWeek(Carbon::SATURDAY);
                    $firstSunday = $date->firstOfYear(Carbon::SUNDAY);

                    if ($sow->year < $date->year) {
                        if ($first->dayOfWeek < 2 || ($first->dayOfWeek == 2 && $lastYearLeapYear == 0)) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($eow->year > $date->year) {
                        if ($last->dayOfWeek > 1 || $last->dayOfWeek == 1 && ($date->year % 4) != 0) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($firstSunday == $first) {
                        return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->isoWeek(null, Carbon::SUNDAY), '2', '0', STR_PAD_LEFT);
                    } else {
                        if ($firstSunday->weekOfYear == 1) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad(($date->weekOfYear - 1), '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->weekOfYear, '2', '0', STR_PAD_LEFT);
                        }
                    }
                    break;
                case 3: //uses same algorithm as mode 3 of WEEK function
                    return $date->isoWeekYear().str_pad($date->isoWeek, '2', '0', STR_PAD_LEFT);
                    break;
                case 4: //uses same algorithm as mode 6 of WEEK function
                    return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->isoWeek(null, Carbon::SUNDAY), '2', '0', STR_PAD_LEFT);
                    break;
                case 5: //uses same algorithm as mode 7 of WEEK function
                    $sow = $date->startOfWeek(Carbon::MONDAY);
                    $eow = $date->endOfWeek(Carbon::SUNDAY);
                    $firstMonday = $date->firstOfYear(Carbon::MONDAY);

                    if ($sow->year < $date->year) { //if the year of the monday at the start of the given week (sow) is the year before the year of the actual date given
                        if ($first->dayOfWeekIso < 3 || ($first->dayOfWeekIso == 3 && $lastYearLeapYear == 0)) { //if the first day of the year (first) is tuesday or monday OR if the first day is wendsday and the last year is a leap year
                            return $date->isoWeekYear(null, Carbon::MONDAY, $sow->addWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::MONDAY, $sow->addWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($eow->year > $date->year) { //if the year of the sunday at the end of the given week (eow) is the year after the year of the actual date given
                        if ($last->dayOfWeekIso > 2 || $last->dayOfWeekIso == 2 && ($date->year % 4) != 0) { //if the last day of the year is (last) is wendsday, thursday, friday, saturday or sunday OR if the last day is tuesday and the current year is a leap year
                            return $date->isoWeekYear(null, Carbon::MONDAY, $sow->subWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::MONDAY, $sow->subWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($firstMonday == $first) { //if the first monday of the year is the same as January 1st of the given year
                        return $date->isoWeekYear(null, Carbon::MONDAY).str_pad($date->weekOfYear, '2', '0', STR_PAD_LEFT);
                    } else {
                        if ($firstMonday->weekOfYear == 1) { //if the week with the first monday on is the first week, then it matches what we want
                            return $date->isoWeekYear(null, Carbon::MONDAY).str_pad($date->weekOfYear, '2', '0', STR_PAD_LEFT);
                        } else { //if the week with the first monday is the second week then we need to subtract a week because it counted the first week as the week before because it have 4 days in it
                            return $date->isoWeekYear(null, Carbon::MONDAY).str_pad(($date->weekOfYear - 1), '2', '0', STR_PAD_LEFT);
                        }
                    }
                    break;
                case 6: //uses same algorithm as mode 6 of WEEK function
                    return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->isoWeek(null, Carbon::SUNDAY), '2', '0', STR_PAD_LEFT);
                    break;
                case 7: //uses same algorithm as mode 7 of WEEK function
                    $sow = $date->startOfWeek(Carbon::MONDAY);
                    $eow = $date->endOfWeek(Carbon::SUNDAY);
                    $firstMonday = $date->firstOfYear(Carbon::MONDAY);

                    if ($sow->year < $date->year) { //if the year of the monday at the start of the given week (sow) is the year before the year of the actual date given
                        if ($first->dayOfWeekIso < 3 || ($first->dayOfWeekIso == 3 && $lastYearLeapYear == 0)) { //if the first day of the year (first) is tuesday or monday OR if the first day is wendsday and the last year is a leap year
                            return $date->isoWeekYear(null, Carbon::MONDAY, $sow->addWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::MONDAY, $sow->addWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($eow->year > $date->year) { //if the year of the sunday at the end of the given week (eow) is the year after the year of the actual date given
                        if ($last->dayOfWeekIso > 2 || $last->dayOfWeekIso == 2 && ($date->year % 4) != 0) { //if the last day of the year is (last) is wendsday, thursday, friday, saturday or sunday OR if the last day is tuesday and the current year is a leap year
                            return $date->isoWeekYear(null, Carbon::MONDAY, $sow->subWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::MONDAY, $sow->subWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($firstMonday == $first) { //if the first monday of the year is the same as January 1st of the given year
                        return $date->isoWeekYear(null, Carbon::MONDAY).str_pad($date->weekOfYear, '2', '0', STR_PAD_LEFT);
                    } else {
                        if ($firstMonday->weekOfYear == 1) { //if the week with the first monday on is the first week, then it matches what we want
                            return $date->isoWeekYear(null, Carbon::MONDAY).str_pad($date->weekOfYear, '2', '0', STR_PAD_LEFT);
                        } else { //if the week with the first monday is the second week then we need to subtract a week because it counted the first week as the week before because it have 4 days in it
                            return $date->isoWeekYear(null, Carbon::MONDAY).str_pad(($date->weekOfYear - 1), '2', '0', STR_PAD_LEFT);
                        }
                    }
                    break;
                default: //same as case 0
                    $sow = $date->startOfWeek(Carbon::SUNDAY);
                    $eow = $date->endOfWeek(Carbon::SATURDAY);
                    $firstSunday = $date->firstOfYear(Carbon::SUNDAY);

                    if ($sow->year < $date->year) {
                        if ($first->dayOfWeek < 2 || ($first->dayOfWeek == 2 && $lastYearLeapYear == 0)) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($eow->year > $date->year) {
                        if ($last->dayOfWeek > 1 || $last->dayOfWeek == 1 && ($date->year % 4) != 0) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear).str_pad('52', '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear).str_pad('53', '2', '0', STR_PAD_LEFT);
                        }
                    } elseif ($firstSunday == $first) {
                        return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->isoWeek(null, Carbon::SUNDAY), '2', '0', STR_PAD_LEFT);
                    } else {
                        if ($firstSunday->weekOfYear == 1) {
                            return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad(($date->weekOfYear - 1), '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->weekOfYear, '2', '0', STR_PAD_LEFT);
                        }
                    }
                    break;
            }
        }
    }
}
