<?php

namespace Mhorninger\MySQLite;

use PDO;
use DateTime;
use DateInterval;
use PHPUnit\Framework\TestCase;
use Mhorninger\SQLite\MySQLiteConnection;

class InjectedMethodTest extends TestCase
{
    private $conn = null;

    public function setUp()
    {
        //The PDO is not necessary to have right now, so we're not going to define it.
        $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        //Set up the connection.
        $this->conn = new MySQLiteConnection($pdo);
    }

    public function testMysqlTimestampDiffSecond()
    {
        $now = new DateTime();
        $plusOneSecond = clone $now;
        $plusOneSecond->add(new DateInterval('PT1S'));
        $nowTimestamp = $now->getTimestamp();
        $plusOneSecondTimestamp = $plusOneSecond->getTimeStamp();
        $query = "select TIMESTAMPDIFF(SECOND, $nowTimestamp, $plusOneSecondTimestamp) AS value";
        $result = $this->conn->selectOne($query);
        $this->assertEquals(1, $result->value);
    }

    public function testGetUTCTimestamp()
    {
        $query = 'SELECT UTC_TIMESTAMP as value';
        $result = $this->conn->selectOne($query);
        $now = new DateTime();
        $expected = $now->getTimestamp();
        $this->assertEqualsWithDelta($expected, $result->value, 1);
    }

    public function testTimeToSec()
    {
        //Queries taken directly from MySQL Documentation.
        $query = "SELECT TIME_TO_SEC('22:23:00') as value";
        $result = $this->conn->selectOne($query);
        $expected = 80580;
        $this->assertEquals($expected, $result->value);
        $query = "SELECT TIME_TO_SEC('00:39:38') as value";
        $result = $this->conn->selectOne($query);
        $expected = 2378;
        $this->assertEquals($expected, $result->value);
    }

    public function testTimeToSecWithBadData()
    {
        //I have seen this happen and was absolutely perplexed as to how we got there.
        $query = 'SELECT TIME_TO_SEC(5) as value';
        $result = $this->conn->selectOne($query);
        $expected = 5;
        $this->assertEquals($expected, $result->value);
    }

    public function testTimeToSecWithNullData()
    {
        //I have seen this happen and was absolutely perplexed as to how we got there.
        $query = 'SELECT TIME_TO_SEC(NULL) as value';
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }
}
