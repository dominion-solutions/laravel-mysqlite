<?php
namespace Mhorninger\SQLite;

use PHPUnit\Framework\TestCase;
use \PDO;

class ConnectionTest extends TestCase
{
    public function testInitializeConnection()
    {
        $pdo = new PDO("sqlite::memory:", null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        //Use the connection
        $conn = new Connection($pdo);
        $result = $conn->selectOne("SELECT BIT_OR(1, 2) AS result");
        $this->assertEquals(3, $result->result);
    }
}
