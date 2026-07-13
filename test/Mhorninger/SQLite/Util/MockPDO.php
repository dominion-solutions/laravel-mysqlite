<?php

namespace Mhorninger\SQLite\Util;

use PDO;

/**
 * Mock PDO object so we can check to make sure we've actually added all of the methods.
 * Credit: http://erichogue.ca/2013/02/best-practices/mocking-pdo-in-phpunit/
 * And Vectorface: https://github.com/Vectorface/MySQLite/blob/master/tests/Vectorface/Tests/MySQLite/Util/FakePDO.php.
 */
class MockPDO extends PDO
{
    public function __construct(string $driver = 'sqlite')
    {
        $this->setAttribute(PDO::ATTR_DRIVER_NAME, $driver);
    }
}
