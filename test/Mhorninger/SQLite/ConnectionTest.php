<?php
namespace Mhorninger\SQLite;

use PHPUnit\Framework\TestCase;
use \Mhorninger\SQLite\MySQLiteConnection as Connection;
use \PDO;
use DateTime;
use DateInterval;

class ConnectionTest extends TestCase
{
    private $conn = null;
    public function setUp()
    {
        //The PDO is not necessary to have right now, so we're not going to define it.
        $pdo = new PDO("sqlite::memory:", null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        //Set up the connection.
        $this->conn = new Connection($pdo);
    }

    /**
     * Test that any function has gotten added.
     * Bitwise OR is the example from Vectorface, so I kept with the tradition.
     */
    public function testInitializeConnection()
    {
        $result = $this->conn->selectOne("SELECT BIT_OR(1, 2) AS result");
        $this->assertEquals(3, $result->result);
    }

    public function testMysqlTimestampDiffSecond()
    {
        $now = new DateTime();
        $plusOneSecond = clone $now;
        $plusOneSecond->add(new DateInterval("PT1S"));
        $nowTimestamp = $now->getTimestamp();
        $plusOneSecondTimestamp = $plusOneSecond->getTimeStamp();
        $query = "select TIMESTAMPDIFF(SECOND, $nowTimestamp, $plusOneSecondTimestamp) AS value";
        $result = $this->conn->selectOne($query);
        $this->assertEquals(1, $result->value);
    }

    public function testGetUTCTimestamp()
    {
        $query = "SELECT UTC_TIMESTAMP as value";
        $result = $this->conn->selectOne($query);
        $now = new DateTime();
        $expected = $now->getTimestamp();
        $this->assertEqualsWithDelta($expected, $result->value, 1);
    }
}
