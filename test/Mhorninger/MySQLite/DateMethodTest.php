<?php

namespace Mhorninger\MySQLite;

use DateTime;
use DateInterval;

class DateMethodTest extends \Mhorninger\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    //region CONVERT_TZ
    public function testConvertTzNamed()
    {
        $query = "SELECT CONVERT_TZ('2004-01-01 12:00:00','GMT','MET') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '2004-01-01 13:00:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testConvertTzNumeric()
    {
        $query = "SELECT CONVERT_TZ('2004-01-01 12:00:00','+00:00','+10:00') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '2004-01-01 22:00:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testConvertTzFromSystem()
    {
        $testTime = '2004-01-01 12:00:00';
        $query = "SELECT CONVERT_TZ('$testTime','SYSTEM','+10:00') as value;";
        $result = $this->conn->selectOne($query);
        $expected = new DateTime($testTime, new \DateTimeZone('+10:00'));
        $expected = '2004-01-01 22:00:00';
        $this->assertEquals($expected, $result->value);
    }

    public function testConvertTzNull()
    {
        $query = "SELECT CONVERT_TZ(NULL,'+00:00','+10:00') as value;";
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    //endregion

    //region DATE_FORMAT
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

    public function testDateFormatWithLiteralColon(){
        $query = "SELECT DATE_FORMAT('2019-02-18 16:46:07', '%Y-%m-%d %H\\:') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '2019-02-18 16:';
        $this->assertEquals($expected, $result->value);
    }

    //endregion

    //region HOUR
    public function testHourNormal()
    {
        $query = "SELECT HOUR('10:05:03') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 10;
        $this->assertEquals($expected, $result->value);
    }

    public function testHourNull()
    {
        $query = 'SELECT HOUR(NULL) as value;';
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    //endregion

    //region MINUTE
    public function testMinute()
    {
        $query = "SELECT MINUTE('2008-02-03 10:05:03') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 5;
        $this->assertEquals($expected, $result->value);
    }

    public function testMinuteNull()
    {
        $query = 'SELECT MINUTE(NULL) as value;';
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    //endregion

    //region TimeStamp Tests
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

    //endregion

    //region TIME_TO_SEC
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

    //endregion

    //region TIMEDIFF
    public function testTimeDiffNegative()
    {
        $query = "SELECT TIMEDIFF('2000-01-01 00:00:00', '2000-01-01 00:00:00.000001') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '-00:00:00.000001';
        $this->assertEquals($expected, $result->value);
    }

    public function testTimeDiff()
    {
        $query = "SELECT TIMEDIFF('2008-12-31 23:59:59.000001','2008-12-30 01:01:01.000002') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '46:58:57.999999';
        $this->assertEquals($expected, $result->value);
    }

    public function testTimeDiffNull()
    {
        $query = "SELECT TIMEDIFF(NULL, '2000:01:01 00:00:00.000001') as value;";
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    //endregion

    //region WEEKDAY
    public function testWeekdayWithTime()
    {
        $query = "SELECT WEEKDAY('2008-02-03 22:23:00') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 6;
        $this->assertEquals($expected, $result->value);
    }

    public function testWeekday()
    {
        $query = "SELECT WEEKDAY('2007-11-06') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }

    public function testWeekdayNull()
    {
        $query = 'SELECT WEEKDAY(NULL) as value;';
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }
    //endregion
}
