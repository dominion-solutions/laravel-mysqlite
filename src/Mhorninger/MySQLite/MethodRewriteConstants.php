<?php
namespace Mhorninger\MySQLite;

class MethodRewriteConstants
{
    const METHOD_REPLACEMENTS = [
        "/(DATE_ADD)(?=.*?, INTERVAL.*?\\))/" => 'datetime',
        "/(?<=datetime.{13}, )INTERVAL (?=.*?\\))/" => '\'+',
        "/(?<=datetime.{22}, )INTERVAL (?=.*?\\))/" => '\'+',
        "/SECOND(?=\\))/" => 'seconds\'',
        "/MINUTE(?=\\))/" => 'minutes\'',
        "/HOUR(?=\\))/" => 'hours\'',
        "/DAY(?=\\))/" => 'days\'',
        "/WEEK(?=\\))/" => 'weeks\'',
        "/MONTH(?=\\))/" => 'months\'',
        "/YEAR(?=\\))/" => 'years\'',
    ];
}
