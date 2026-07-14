<?php

namespace Mhorninger\MySQLite;

use Mhorninger\MySQLite\MySQL\DateTimeExtended;
use Mhorninger\MySQLite\MySQL\Miscellaneous;
use Mhorninger\MySQLite\MySQL\NumericExtended;
use Mhorninger\MySQLite\MySQL\StringExtended;
use PDO;
use ReflectionClass;
use ReflectionMethod;

/**
 * MySQLite is the extension Vectorface's MySQLite extension.
 *
 * @see \Vectorface\MySQLite\MySQLite
 */
class MySQLite extends \Vectorface\MySQLite\MySQLite
{
    use DateTimeExtended;
    use Miscellaneous;
    use NumericExtended;
    use StringExtended;

    /**
     * Get information about functions that are meant to be exposed by this class.
     *
     * @return array<string, int> An associative array composed of function names mapping to accepted parameter counts.
     */
    protected static function getPublicMethodData(): array
    {
        $data = [];

        $ref = new ReflectionClass(self::class);
        $methods = $ref->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC);
        foreach ($methods as $method) {
            if (! str_starts_with($method->name, 'mysql_')) {
                continue;
            }

            $data[$method->name] = $method->getNumberOfRequiredParameters();
        }

        return $data;
    }

    /**
     * Add MySQLite compatibility functions to a PDO object.
     *
     * @param  \PDO  $pdo  A PDO instance to which the MySQLite compatibility functions should be added.
     * @param  string[]|null  $fnList  A list of functions to create on the SQLite database. (Omit to create all.)
     * @return \PDO Returns a reference to the PDO instance passed in to the function.
     */
    public static function &createFunctions(\PDO &$pdo, ?array $fnList = null): \PDO
    {
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            throw new \InvalidArgumentException('Expecting a PDO instance using the SQLite driver');
        }

        foreach (static::getPublicMethodData() as $method => $paramCount) {
            static::registerMethod($pdo, $method, $paramCount, $fnList);
        }

        return $pdo;
    }

    /**
     * Register a method as an SQLite funtion.
     *
     * @param  \PDO  $pdo  A PDO instance to which the MySQLite compatibility functions should be added.
     * @param  string  $method  The internal method name.
     * @param  int  $paramCount  The suggested parameter count.
     * @param  string[]|null  $fnList  A list of functions to create on the SQLite database, or empty for all.
     * @return bool Returns true if the method was registed. False otherwise.
     */
    protected static function registerMethod(\PDO &$pdo, $method, $paramCount, ?array $fnList = null): bool
    {
        $function = substr($method, 6); /* Strip 'mysql_' prefix to get the function name. */

        /* Skip functions not in the list. */
        if (! empty($fnList) && ! in_array($function, $fnList)) {
            return false;
        }

        if ($paramCount) {
            return $pdo->sqliteCreateFunction($function, self::{$method}(...), $paramCount);
        }

        return $pdo->sqliteCreateFunction($function, self::{$method}(...));
    }
}
