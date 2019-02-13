<?php

namespace Mhorninger\MySQLite;

class MethodRewriteConstants
{
    const METHOD_REPLACEMENTS = [
        '/(DATE_ADD)(?=.*?, INTERVAL.*?\\))/' => 'datetime',
        '/INTERVAL (?=.*?\\))/' => '\'+',
        '/INTERVAL (?=.*?\\))/' => '\'+',
        '/SECOND(?=\\))/' => 'seconds\'',
        '/MINUTE(?=\\))/' => 'minutes\'',
        '/HOUR(?=\\))/' => 'hours\'',
        '/DAY(?=\\))/' => 'days\'',
        '/WEEK(?=\\))/' => 'weeks\'',
        '/MONTH(?=\\))/' => 'months\'',
        '/YEAR(?=\\))/' => 'years\'',
    ];
}
