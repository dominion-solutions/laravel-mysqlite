<?php

namespace Mhorninger\SQLite;

use PDO;
use PHPUnit\Framework\TestCase;
use Mhorninger\SQLite\MySQLiteConnection as Connection;

class ConnectionTest extends TestCase
{
    private $conn = null;

    public function setUp()
    {
        //The PDO is not necessary to have right now, so we're not going to define it.
        $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        //Set up the connection.
        $this->conn = new Connection($pdo);
    }

    /**
     * Test that any function has gotten added.
     * Bitwise OR is the example from Vectorface, so I kept with the tradition.
     */
    public function testInitializeConnection()
    {
        $result = $this->conn->selectOne('SELECT BIT_OR(1, 2) AS result');
        $this->assertEquals(3, $result->result);
    }
}
