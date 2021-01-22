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
        if($date) {
            $date = CarbonImmutable::parse($date);
            $first = $date->firstOfYear();

            switch($mode) {
                case 0: //starts on sunday, first week is first sunday and goes 0-53
                    $sow = $first->startOfWeek(Carbon::SUNDAY);
                    if (($date->month == 1 && $date->isoWeek(null, Carbon::SUNDAY) >= 52) || ($first->dayOfWeek >= 1 && $date->isoWeek(null, Carbon::SUNDAY) == 1)){ //checks if it is rounding a week of the next year back to 53 and changes it to allows 0 week
                        return '0';
                    } else if ($date->month == 12 && $date->isoWeek(null, Carbon::SUNDAY) == 1){ //check if it is rounging a week form the previous year to 1 
                        return '53';
                    } else {
                        if ($sow->year < $first->year) { //checks is the monday before the 1st of the year is in this year or last year
                            return $date->isoWeek(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear);
                        } else {
                            return $date->isoWeek(null, Carbon::SUNDAY, $sow->dayOfYear);
                        }
                    }
                    break;
                case 1: //DONE starts on monday, first week is one with 4 or more days and goes 0-53
                    if ($date->month == 1 && $date->isoWeek >= 52){ //checks if it is rounding a week of the next year back to 53 and changes it to allows 0 week
                        return '0';
                    } else if ($date->month == 12 && $date->isoWeek == 1){ //check if it is rounging a week form the previous year to 1 
                        return '53';
                    } else {
                        return $date->isoWeek();
                    }
                    break;
                case 2: //starts on sunday, first week is first sunday and goes 1-53
                    $sow = $first->startOfWeek(Carbon::SUNDAY);
                    if ($date->month == 12 && $date->isoWeek(null, Carbon::SUNDAY) == 1){ //check if it is rounging a week form the previous year to 1 
                        return '53';
                    } else {
                        if ($sow->year < $first->year) { //checks is the monday before the 1st of the year is in this year or last year
                            if($first->dayOfWeek >= 3 && $date->month != 12){
                                return $date->isoWeek(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear);
                            }
                            return $date->isoWeek(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear); //<<<testWeekMode2Again2 fails here
                        } else {
                            return $date->isoWeek(null, Carbon::SUNDAY, $sow->dayOfYear);
                        }
                    }
                    break;
                case 3: //DONE starts on monday, first week is one with 4 or more days and goes 1-53 (same as base algorithm)
                    return $date->isoWeek; 
                    break;
                case 4: //DONE starts on sunday, first week is one with 4 or more days and goes 0-53
                    if ($date->month == 1 && $date->isoWeek(null, Carbon::SUNDAY) >= 52){ //checks if it is rounding a week of the next year back to 53 and changes it to allows 0 week
                        return '0';
                    } else if ($date->month == 12 && $date->isoWeek(null, Carbon::SUNDAY) == 1){
                        return '53';
                    } else{
                        return $date->isoWeek(null, Carbon::SUNDAY);
                    }
                    break;
                case 5: //starts on monday, first week is first monday and goes 0-53
                    $sow = $date->startOfWeek(Carbon::MONDAY);
                    $lastYearLeapYear = ($date->year - 1) % 4;
                    $week = round(($date->dayOfYear / 7), 0, PHP_ROUND_HALF_DOWN);

                    if ($week == 0){
                        if ($sow->year < $date->year && $first->dayOfWeekIso != 1){
                            // if (($date->month == 1 && $date->isoWeek >= 52)){ //checks if it is rounding a week of the next year back to 53 and changes it to allows 0 week
                            //     return '0';
                            // } else if ($date->month == 12 && $date->isoWeek == 1){ //check if it is rounging a week form the previous year to 1 
                            //     return '53';
                            // } else {
                            //     return $date->isoWeek();
                            // }
                            
                            // if ($first->dayOfWeekIso < 3 || ($first->dayOfWeekIso == 3 && $lastYearLeapYear == 0)){
                            //     return '53';
                            // } else {
                            //     return '52';
                            // }

                            return $week;
                        } else {
                            return $date->isoWeek;
                        }
                    } else {
                        if (($date->dayOfWeekIso == 1 && $date->dayOfYear == 365) || ($date->dayOfWeekIso <= 2 && $date->dayOfYear == 366)){
                            return $week+1;
                        }
                        return $week;
                    }
                    break;
                case 6: //DONE starts on sunday, first week is one with 4 or more days and goes 1-53
                    return $date->isoWeek(null, Carbon::SUNDAY);
                    break;
                case 7: //DONE? starts on monday, first week is first monday and goes 1-53
                    $sow = $date->startOfWeek(Carbon::MONDAY);
                    $lastYearLeapYear = ($date->year - 1) % 4;
                    $week = round(($date->dayOfYear / 7), 0, PHP_ROUND_HALF_DOWN);

                    if ($sow->year < $date->year){
                        if ($first->dayOfWeekIso < 3 || ($first->dayOfWeekIso == 3 && $lastYearLeapYear == 0)){ //if first is less then monday or tuesday OR if first is wendsday amd ;ast year was a leap year
                            return '53';
                        } else {
                            return '52';
                        }
                    } else if ($first->dayOfWeekIso != 1) {
                        if ($sow->dayOfYear < 8) {
                            return round((($date->dayOfYear - ($date->dayOfYear % $sow->dayOfYear)) / 7), 0, PHP_ROUND_HALF_DOWN);
                        } else {
                            return $week; //fails testWeek7Layover23
                        }
                    } else {
                        return $date->isoWeek; //fails testWeek7Layover16 and testWeek7Layover24
                    }

                    // if ($week == 0){ //the rounded week value is 0 (rounds down if less then 0.5 meaning if it less then 4 days left over)
                    //     if ($sow->year < $date->year && $first->dayOfWeekIso != 1){ //if the year of the monday at the start of the week is les then the year of the provided date and if the first day of week isnt a monday
                    //         if ($first->dayOfWeekIso < 3 || ($first->dayOfWeekIso == 3 && $lastYearLeapYear == 0)){ //if first is less then monday or tuesday OR if first is wendsday amd ;ast year was a leap year
                    //             return '53';
                    //         } else {
                    //             return '52';
                    //         }
                    //     } else { //if the first day is monday or if the sow year and date year are the same
                    //         return $date->isoWeek;
                    //     }
                    // } else { //if week is anything but 0
                    //     if (($date->dayOfWeekIso == 1 && $date->dayOfYear == 365) || ($date->dayOfWeekIso <= 2 && $date->dayOfYear == 366)){
                    //         return $week+1;
                    //     } 
                    //     return $week; //fails testWeek7Again4, testWeek7Again6 and testWeek7Again8
                    // }
                    break;   
                default: 
                    $sow = $first->startOfWeek(Carbon::SUNDAY);
                    if ($date->month == 1 && $date->isoWeek(null, Carbon::SUNDAY) >= 52 || ($first->dayOfWeek >= 1 && $date->isoWeek(null, Carbon::SUNDAY) == 1)){ //checks if it is rounding a week of the next year back to 53 and changes it to allows 0 week
                        return '0';
                    } else if ($date->month == 12 && $date->isoWeek(null, Carbon::SUNDAY) == 1){ //check if it is rounging a week form the previous year to 1 
                        return '53';
                    } else {
                        if ($sow->year < $first->year) { //checks is the monday before the 1st of the year is in this year or last year
                            return $date->isoWeek(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear);
                        } else {
                            return $date->isoWeek(null, Carbon::SUNDAY, $sow->dayOfYear);
                        }
                    }
                    break;
            }
        }
    }

    //phpcs:disable
    public static function mysql_yearweek($date, $mode)
    {
        if($date) {
            $date = CarbonImmutable::parse($date);
            $first = $date->firstOfYear();

            switch($mode) {
                case 0: 
                    $sow = $first->startOfWeek(Carbon::SUNDAY);
                    if ($date->month == 12 && $date->isoWeek(null, Carbon::SUNDAY) == 1){ //check if it is rounging a week form the previous year to 1 
                        return $date->isoWeekYear().'53';
                    } else {
                        if ($sow->year < $first->year) { //checks is the monday before the 1st of the year is in this year or last year
                            // if($first->dayOfWeek >= 3 && $date->month != 12){
                            //     return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear).str_pad($date->isoWeek(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear), '2', '0', STR_PAD_LEFT);
                            // }
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear).str_pad($date->isoWeek(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear), '2', '0', STR_PAD_LEFT); //<< testYearweekMode0Layover6 fails here
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->dayOfYear).str_pad($date->isoWeek(null, Carbon::SUNDAY, $sow->dayOfYear), '2', '0', STR_PAD_LEFT);
                        }
                    }
                    break;
                case 1: //DONE
                    return $date->isoWeekYear().str_pad($date->isoWeek, '2', '0', STR_PAD_LEFT); //uses same algorithm as mode 3 of WEEK function (starts on monday, first week is one with 4 or more days and goes 1-53)
                    break;
                case 2: //DONE
                    $sow = $first->startOfWeek(Carbon::SUNDAY);
                    if ($date->month == 12 && $date->isoWeek(null, Carbon::SUNDAY) == 1){ //check if it is rounging a week form the previous year to 1 
                        return $date->isoWeekYear().'53';
                    } else {
                        if ($sow->year < $first->year) { //checks is the monday before the 1st of the year is in this year or last year
                            // if($first->dayOfWeek >= 3 && $date->month != 12){
                            //     return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear).str_pad($date->isoWeek(null, Carbon::SUNDAY, $sow->subWeek()->dayOfYear), '2', '0', STR_PAD_LEFT);
                            // }
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear).str_pad($date->isoWeek(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear), '2', '0', STR_PAD_LEFT); //<< testYearweekMode2Layover6 fails here
                        } else {
                            return $date->isoWeekYear(null, Carbon::SUNDAY, $sow->dayOfYear).str_pad($date->isoWeek(null, Carbon::SUNDAY, $sow->dayOfYear), '2', '0', STR_PAD_LEFT);
                        }
                    }
                    break;
                case 3: //DONE
                    return $date->isoWeekYear().str_pad($date->isoWeek, '2', '0', STR_PAD_LEFT); //uses same algorithm as mode 3 of WEEK function (starts on monday, first week is one with 4 or more days and goes 1-53)
                    break;
                case 4: //DONE
                    return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->isoWeek(null, Carbon::SUNDAY), '2', '0', STR_PAD_LEFT);
                    break;
                case 5: 
                    $sow = $first->startOfWeek(Carbon::MONDAY); 
                    if ($sow->year < $first->year) { //checks is the monday before the 1st of the year is in this year or last year
                        // if($first->dayOfWeekIso >= 4 && $date->month != 12){
                        //     return $date->isoWeekYear(null, Carbon::MONDAY, $sow->subWeek()->dayOfYear).str_pad($date->isoWeek(null, Carbon::MONDAY, $sow->subWeek()->dayOfYear), '2', '0', STR_PAD_LEFT);
                        // }
                        return $date->isoWeekYear(null, Carbon::MONDAY, $sow->addWeek()->dayOfYear).str_pad($date->isoWeek(null, Carbon::MONDAY, $sow->addWeek()->dayOfYear), '2', '0', STR_PAD_LEFT); //<< testYearweekMode5Layover5
                    } else {
                        return $date->isoWeekYear(null, Carbon::MONDAY, $sow->dayOfYear).str_pad($date->isoWeek(null, Carbon::MONDAY, $sow->dayOfYear), '2', '0', STR_PAD_LEFT); //<< testYearWeekMode5Layover
                    }
                    break;
                case 6: // DONE
                    return $date->isoWeekYear(null, Carbon::SUNDAY).str_pad($date->isoWeek(null, Carbon::SUNDAY), '2', '0', STR_PAD_LEFT);
                    break;
                case 7: //DONE
                    $sow = $first->startOfWeek(Carbon::MONDAY);
                    if ($sow->year < $first->year) { //checks is the monday before the 1st of the year is in this year or last year
                        // if($first->dayOfWeekIso >= 4 && $date->month != 12){
                        //     return $date->isoWeekYear(null, Carbon::MONDAY, $sow->subWeek()->dayOfYear).str_pad($date->isoWeek(null, Carbon::MONDAY, $sow->subWeek()->dayOfYear), '2', '0', STR_PAD_LEFT);
                        // }
                        return $date->isoWeekYear(null, Carbon::MONDAY, $sow->addWeek()->dayOfYear).str_pad($date->isoWeek(null, Carbon::MONDAY, $sow->addWeek()->dayOfYear), '2', '0', STR_PAD_LEFT); //<<testYearweekMode7Layover6
                    } else {
                        return $date->isoWeekYear(null, Carbon::MONDAY, $sow->dayOfYear).str_pad($date->isoWeek(null, Carbon::MONDAY, $sow->dayOfYear), '2', '0', STR_PAD_LEFT);
                    }
                    break;  
                default:
                    $sow = $first->startOfWeek(Carbon::SUNDAY);
                    if ($date->month == 1 && $date->isoWeek(null, Carbon::SUNDAY) >= 52 || ($first->dayOfWeek >= 1 && $date->isoWeek(null, Carbon::SUNDAY) == 1)){ //checks if it is rounding a week of the next year back to 53 and changes it to allows 0 week
                        return $date->isoWeekYear().'00';
                    } else if ($date->month == 12 && $date->isoWeek(null, Carbon::SUNDAY) == 1){ //check if it is rounging a week form the previous year to 1 
                        return $date->isoWeekYear().'53';
                    } else {
                        if ($sow->year < $first->year) { //checks is the monday before the 1st of the year is in this year or last year
                            return $date->isoWeekYear().str_pad($date->isoWeek(null, Carbon::SUNDAY, $sow->addWeek()->dayOfYear), '2', '0', STR_PAD_LEFT);
                        } else {
                            return $date->isoWeekYear().str_pad($date->isoWeek(null, Carbon::SUNDAY, $sow->dayOfYear), '2', '0', STR_PAD_LEFT);
                        }
                    }
                    break;
            }
        }
    }
}
