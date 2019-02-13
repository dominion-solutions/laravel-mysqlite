<?php

namespace Mhorninger\MySQLite;

use Mhorninger\TestCase;

class MethodRewriteTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    //region DATE_ADD()
    public function testDateAddReplacementSecond()
    {
        $query = "SELECT DATE_ADD('1983-12-08 15:00:00', INTERVAL 1 SECOND) as value";
        $result = $this->conn->selectOne($query);
        $expected = '1983-12-08 15:00:01';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateAddReplacementMinute()
    {
        $query = "SELECT DATE_ADD('1983-12-08 15:00:00', INTERVAL 1 MINUTE) as value";
        $result = $this->conn->selectOne($query);
        $expected = '1983-12-08 15:01:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateAddReplacementHour()
    {
        $query = "SELECT DATE_ADD('1983-12-08 15:00:00', INTERVAL 1 HOUR) as value";
        $result = $this->conn->selectOne($query);
        $expected = '1983-12-08 16:00:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateAddReplacementDay()
    {
        $query = "SELECT DATE_ADD('1983-12-08', INTERVAL 1 DAY) as value";
        $result = $this->conn->selectOne($query);
        $expected = '1983-12-09 00:00:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateAddReplacementMonth()
    {
        $query = "SELECT DATE_ADD('1983-12-08', INTERVAL 1 MONTH) as value";
        $result = $this->conn->selectOne($query);
        $expected = '1984-01-08 00:00:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateAddReplacementYear()
    {
        $query = "SELECT DATE_ADD('1983-12-08', INTERVAL 1 YEAR) as value";
        $result = $this->conn->selectOne($query);
        $expected = '1984-12-08 00:00:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateAddEdgeCaseFromDrLongGhost()
    {
        $query = "SELECT DATE_ADD(CONVERT_TZ('2019-01-01 00:00:00', 'GMT', 'SYSTEM'), INTERVAL 30 MINUTE) AS value";
        $result = $this->conn->selectOne($query);
        $expected = '2019-01-01 00:30:00';
        $this->assertEquals($expected, $result->value);
    }

    //endregion
}
