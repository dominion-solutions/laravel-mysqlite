<?php

namespace Mhorninger\MySQLite\MySQL;

use NumberFormatter;

trait StringExtended
{
    /**
     * Format a number according to the nubmer of decimals provided and culture.
     * 
     * @param mixed... number, decimals, culture.
     */
    // phpcs:disable
    public static function mysql_format()
    {
        //phpcs:enable
        $args = func_get_args();
        $length = count($args);
        if ($args && 0 < $length && ($number = $args[0]) != null) {
            $decimals = 1 < $length ? $args[1] : 0;
            $culture = 2 < $length ? $args[2] : 'en_US';
            $pattern = '#,##0';
            if ($decimals > 0) {
                $pattern = $pattern.'.';
                $base = strlen($pattern);
                $decimals = $base + $decimals;
                $pattern = str_pad($pattern, $decimals, '0', STR_PAD_RIGHT);
            }
            $formatter = new NumberFormatter($culture, NumberFormatter::PATTERN_DECIMAL, $pattern);

            return $formatter->format($number);
        }
    }

    // phpcs:disable
    public static function mysql_lpad($string, $length, $pad)
    {
        //phpcs:enable
        if (isset($string, $length, $pad)) {
            if (strlen($string) < $length) {
                return str_pad($string, $length, $pad, STR_PAD_LEFT);
            }

            return substr($string, 0, $length);
        }
    }

    // phpcs:disable
    public static function mysql_rpad($string, $length, $pad)
    {
        //phpcs:enable
        if (isset($string, $length, $pad)) {
            if (strlen($string) < $length) {
                return str_pad($string, $length, $pad, STR_PAD_RIGHT);
            }

            return substr($string, 0, $length);
        }
    }

    public static function mysql_left($string, $length)
    {
        return substr($string, 0, $length);
    }

    public static function mysql_right($string, $length)
    {
        return substr($string, -$length);
    }
}
