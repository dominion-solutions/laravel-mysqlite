<?php

namespace Mhorninger\MySQLite;

use PDO;
use ReflectionClass;
use ReflectionMethod;
use Mhorninger\MySQLite\MySQL\DateTimeExtended;

/**
 * MySQLite is the extension Vectorface's MySQLite extension.
 * @see \Vectorface\MySQLite\MySQLite
 */
class MySQLite extends \Vectorface\MySQLite\MySQLite
{
    use DateTimeExtended;

    /**
     * Get information about functions that are meant to be exposed by this class.
     *
     * @return int[] An associative array composed of function names mapping to accepted parameter counts.
     */
    protected static function getPublicMethodData()
    {
        $data = [];

        $ref = new ReflectionClass(__CLASS__);
        $methods = $ref->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC);
        foreach ($methods as $method) {
            if (strpos($method->name, 'mysql_') !== 0) {
                continue;
            }

            $data[$method->name] = $method->getNumberOfRequiredParameters();
        }

        return $data;
    }

    /**
     * Add MySQLite compatibility functions to a PDO object.
     *
     * @param \PDO $pdo    A PDO instance to which the MySQLite compatibility functions should be added.
     * @param string[] $fnList A list of functions to create on the SQLite database. (Omit to create all.)
     * @return \PDO Returns a reference to the PDO instance passed in to the function.
     */
    public static function &createFunctions(\PDO &$pdo, array $fnList = null)
    {
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            throw new InvalidArgumentException('Expecting a PDO instance using the SQLite driver');
        }

        foreach (static::getPublicMethodData() as $method => $paramCount) {
            static::registerMethod($pdo, $method, $paramCount, $fnList);
        }

        return $pdo;
    }

    /**
     * Register a method as an SQLite funtion.
     *
     * @param PDO $pdo        A PDO instance to which the MySQLite compatibility functions should be added.
     * @param string $method     The internal method name.
     * @param int $paramCount The suggested parameter count.
     * @param string[] $fnList     A list of functions to create on the SQLite database, or empty for all.
     * @return bool Returns true if the method was registed. False otherwise.
     */
    protected static function registerMethod(\PDO &$pdo, $method, $paramCount, array $fnList = null)
    {
        $function = substr($method, 6); /* Strip 'mysql_' prefix to get the function name. */

        /* Skip functions not in the list. */
        if (! empty($fnList) && ! in_array($function, $fnList)) {
            return false;
        }

        if ($paramCount) {
            return $pdo->sqliteCreateFunction($function, [__CLASS__, $method], $paramCount);
        }

        return $pdo->sqliteCreateFunction($function, [__CLASS__, $method]);
    }
}
