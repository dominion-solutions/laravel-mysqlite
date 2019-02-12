<?php
namespace Mhorninger\MySQLite;

use DateTime;
use DateInterval;

class DateMethodTest extends \Mhorninger\TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    #region DATE_FORMAT
    public function testDateFormatWMY()
    {
        $query = "SELECT DATE_FORMAT('2009-10-04 22:23:00', '%W %M %Y') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 'Sunday October 2009';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateFormatHis()
    {
        $query = "SELECT DATE_FORMAT('2007-10-04 22:23:00', '%H:%i:%s') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '22:23:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateFormatDyadmbj()
    {
        $query = "SELECT DATE_FORMAT('1900-10-04 22:23:00', '%D %y %a %d %m %b %j') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '4th 00 Thu 04 10 Oct 276';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateFormaHkIrTSw()
    {
        $query = "SELECT DATE_FORMAT('1997-10-04 22:23:00', '%H %k %I %r %T %S %w') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '22 22 10 10:23:00 PM 22:23:00 00 6';
        $this->assertEquals($expected, $result->value);
    }

    public function testDateFormatD()
    {
        $query = "SELECT DATE_FORMAT('2006-06-01', '%d') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '01';
        $this->assertEquals($expected, $result->value);
    }
    public function testDateFormatNull()
    {
        $query = "SELECT DATE_FORMAT('2019-02-12', NULL) as value;";
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }
    #endregion

    #region MINUTE
    public function testMinute()
    {
        $query = "SELECT MINUTE('2008-02-03 10:05:03') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 5;
        $this->assertEquals($expected, $result->value);
    }
    #endregion
    #region TimeStamp Tests
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
    #endregion

    #region TIME_TO_SEC
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
    #endregion
}
