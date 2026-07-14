<?php

namespace Mhorninger\MySQLite;

class MethodRewriteConstants
{
    public const METHOD_REPLACEMENTS = [
        ['/(DATE_ADD)(?=.*?, INTERVAL.*?\\))/', 'datetime'],
        ['/INTERVAL (?=.*?\\))/', '\'+'],
        ['/INTERVAL (?=.*?\\))/', '\'+'],
        ['/SECOND(?=\\))/', 'seconds\''],
        ['/MINUTE(?=\\))/', 'minutes\''],
        ['/HOUR(?=\\))/', 'hours\''],
        ['/DAY(?=\\))/', 'days\''],
        ['/WEEK(?=\\))/', 'weeks\''],
        ['/MONTH(?=\\))/', 'months\''],
        ['/YEAR(?=\\))/', 'years\''],
        ['/LEFT(?=.*?, .*?\\))/', '`LEFT`'],
        ['/RIGHT(?=.*?, .*?\\))/', '`RIGHT`'],
    ];
}
