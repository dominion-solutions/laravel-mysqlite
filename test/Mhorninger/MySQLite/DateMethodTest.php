<?php

namespace Mhorninger\MySQLite;

use DateInterval;
use DateTime;

class DateMethodTest extends \Mhorninger\TestCase
{
    public function setUp(): void
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

    // region WEEK
    public function testWeekNull()
    {
        $query = "SELECT WEEK(NULL, 0) as value;";
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    // public function testWeekNoMode()
    // {
    //     $query = "SELECT WEEK(NULL) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $this->assertNull($result->value);
    // }

    // public function testWeekMode0()
    // {
    //     $query = "SELECT WEEK('2011-10-31', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 44;
    //     $this->assertEquals($expected, $result->value);
    // }

    public function testWeekMode1()
    {
        $query = "SELECT WEEK('2003-03-03', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 10;
        $this->assertEquals($expected, $result->value);
    }

    // public function testWeekMode2()
    // {
    //     $query = "SELECT WEEK('2021-08-16', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 33;
    //     $this->assertEquals($expected, $result->value);
    // }

    public function testWeekMode3()
    {
        $query = "SELECT WEEK('2000-02-29', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 9;
        $this->assertEquals($expected, $result->value);
    }

    public function testWeekMode4()
    {
        $query = "SELECT WEEK('2002-04-27', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 17;
        $this->assertEquals($expected, $result->value);
    }

    // public function testWeekMode5()
    // {
    //     $query = "SELECT WEEK('2019-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }

    public function testWeekMode6()
    {
        $query = "SELECT WEEK('1996-06-15', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 24;
        $this->assertEquals($expected, $result->value);
    }

    public function testWeekMode7()
    {
        $query = "SELECT WEEK('2014-11-21', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 46;
        $this->assertEquals($expected, $result->value);
    }

    // public function testWeekMode0Again()
    // {
    //     $query = "SELECT WEEK('2019-01-07', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Again2()
    // {
    //     $query = "SELECT WEEK('2011-10-31', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 44;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Again3()
    // {
    //     $query = "SELECT WEEK('2008-02-20', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 7;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Again4()
    // {
    //     $query = "SELECT WEEK('1987-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Again5()
    // {
    //     $query = "SELECT WEEK('2010-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }

    public function testWeekMode1Again()
    {
        $query = "SELECT WEEK('2019-01-07', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Again2()
    {
        $query = "SELECT WEEK('2015-06-15', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 25;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Again3()
    {
        $query = "SELECT WEEK('2007-12-27', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Again4()
    {
        $query = "SELECT WEEK('2003-01-11', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Again5()
    {
        $query = "SELECT WEEK('1996-12-29', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }

    // public function testWeekMode2Again()
    // {
    //     $query = "SELECT WEEK('2019-01-07', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode2Again2()
    // {
    //     $query = "SELECT WEEK('1987-01-01', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode2Again3()
    // {
    //     $query = "SELECT WEEK('2010-01-01', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }

    public function testWeekMode3Again()
    {
        $query = "SELECT WEEK('2019-01-07', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Again2()
    {
        $query = "SELECT WEEK('2015-06-15', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 25;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Again3()
    {
        $query = "SELECT WEEK('2007-12-27', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Again4()
    {
        $query = "SELECT WEEK('2003-01-11', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Again5()
    {
        $query = "SELECT WEEK('1996-12-29', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }

    public function testWeekMode4Again()
    {
        $query = "SELECT WEEK('2019-01-07', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Again2()
    {
        $query = "SELECT WEEK('2015-06-15', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 24;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Again3()
    {
        $query = "SELECT WEEK('2007-12-27', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Again4()
    {
        $query = "SELECT WEEK('2003-01-11', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Again5()
    {
        $query = "SELECT WEEK('1996-12-29', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }

    // public function testWeekMode5Again()
    // {
    //     $query = "SELECT WEEK('2019-01-07', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Again2()
    // {
    //     $query = "SELECT WEEK('2015-06-15', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 24;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Again3()
    // {
    //     $query = "SELECT WEEK('2007-12-27', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Again4()
    // {
    //     $query = "SELECT WEEK('2003-01-11', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Again5()
    // {
    //     $query = "SELECT WEEK('1996-12-29', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }

    public function testWeekMode6Again()
    {
        $query = "SELECT WEEK('2019-01-07', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Again2()
    {
        $query = "SELECT WEEK('2015-06-15', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 24;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Again3()
    {
        $query = "SELECT WEEK('2007-12-27', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Again4()
    {
        $query = "SELECT WEEK('2003-01-11', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Again5()
    {
        $query = "SELECT WEEK('1996-12-29', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }

    public function testWeekMode7Again()
    {
        $query = "SELECT WEEK('2019-01-07', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again2()
    {
        $query = "SELECT WEEK('2015-06-15', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 24;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again3()
    {
        $query = "SELECT WEEK('2007-12-27', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again4()
    {
        $query = "SELECT WEEK('2003-01-11', 7) as value;"; //year start wendsday, day is saturday
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again5()
    {
        $query = "SELECT WEEK('1996-12-29', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again6()
    {
        $query = "SELECT WEEK('2002-01-11', 7) as value;"; //year starts tuesday, day is friday
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again7()
    {
        $query = "SELECT WEEK('2007-01-11', 7) as value;"; //year starts monday, day is thursday *
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again8()
    {
        $query = "SELECT WEEK('2004-01-11', 7) as value;"; //year starts thursday, day is sunday
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again9()
    {
        $query = "SELECT WEEK('2000-01-11', 7) as value;"; //year starts saturday, day is tuesday *
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again10()
    {
        $query = "SELECT WEEK('1999-01-11', 7) as value;"; //year starts friday, day is monday *
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Again11()
    {
        $query = "SELECT WEEK('2006-01-11', 7) as value;"; //year starts sunday, day is wendsday *
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }

    // public function testWeekMode0Layover()
    // {
    //     $query = "SELECT WEEK('2017-12-31', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 53;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Layover2()
    // {
    //     $query = "SELECT WEEK('2018-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Layover3()
    // {
    //     $query = "SELECT WEEK('2016-12-31', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Layover4()
    // {
    //     $query = "SELECT WEEK('2017-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Layover5()
    // {
    //     $query = "SELECT WEEK('2007-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode0Layover6()
    // {
    //     $query = "SELECT WEEK('2000-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    
    public function testWeekMode1Layover() // monday start of year
    {
        $query = "SELECT WEEK('2018-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover2() //tuesday start of year
    {
        $query = "SELECT WEEK('2019-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover3() // wendsday start of year
    {
        $query = "SELECT WEEK('2020-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover4() // thursday start of year
    {
        $query = "SELECT WEEK('2015-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover5() // friday start of year
    {
        $query = "SELECT WEEK('2016-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover6() // saturday start of year
    {
        $query = "SELECT WEEK('2022-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover7() // sunday start of year
    {
        $query = "SELECT WEEK('2023-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover8() // monday start of year after leap year
    {
        $query = "SELECT WEEK('2001-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover9() // tuesday start of year after leap year
    {
        $query = "SELECT WEEK('2013-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover10() // wendsday start of year after leap year
    {
        $query = "SELECT WEEK('2025-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover11() // thursday start of year after leap year
    {
        $query = "SELECT WEEK('2009-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover12() // friday start of year after leap year
    {
        $query = "SELECT WEEK('1993-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover13() // saturday start of year after leap year
    {
        $query = "SELECT WEEK('2005-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover14() // sunday start of year after leap year
    {
        $query = "SELECT WEEK('2017-01-01', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover15() // sunday end of year
    {
        $query = "SELECT WEEK('2017-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover16() //monday end of year
    {
        $query = "SELECT WEEK('2018-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover17() // tuesday end of year
    {
        $query = "SELECT WEEK('2019-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover18() // wendsday end of year
    {
        $query = "SELECT WEEK('2014-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover19() //thursday end of year
    {
        $query = "SELECT WEEK('2015-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover20() // friday end of year
    {
        $query = "SELECT WEEK('2021-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover21() // saturday end of year
    {
        $query = "SELECT WEEK('2022-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover22() // sunday end of year of leap year
    {
        $query = "SELECT WEEK('2000-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover23() // monday end of year of leap year
    {
        $query = "SELECT WEEK('2012-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover24() // tuesday end of year of leap year
    {
        $query = "SELECT WEEK('2024-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover25() // wendsday end of year of leap year
    {
        $query = "SELECT WEEK('2008-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover26() // thursday end of year of leap year
    {
        $query = "SELECT WEEK('1992-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover27() // friday end of year of leap year
    {
        $query = "SELECT WEEK('2004-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode1Layover28() // saturday end of year of leap year
    {
        $query = "SELECT WEEK('2016-12-31', 1) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }

    // public function testWeekMode2Layover()
    // {
    //     $query = "SELECT WEEK('2018-01-02', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 53;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode2Layover2()
    // {
    //     $query = "SELECT WEEK('2017-12-31', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 53;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode2Layover3()
    // {
    //     $query = "SELECT WEEK('2018-01-07', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode2Layover4()
    // {
    //     $query = "SELECT WEEK('2016-12-31', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode2Layover5()
    // {
    //     $query = "SELECT WEEK('2017-01-01', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }

    public function testWeekMode3Layover() // monday start of year
    {
        $query = "SELECT WEEK('2018-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover2() //tuesday start of year
    {
        $query = "SELECT WEEK('2019-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover3() // wendsday start of year
    {
        $query = "SELECT WEEK('2020-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover4() // thursday start of year
    {
        $query = "SELECT WEEK('2015-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover5() // friday start of year
    {
        $query = "SELECT WEEK('2016-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover6() // saturday start of year
    {
        $query = "SELECT WEEK('2022-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover7() // sunday start of year
    {
        $query = "SELECT WEEK('2023-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover8() // monday start of year after leap year
    {
        $query = "SELECT WEEK('2001-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover9() // tuesday start of year after leap year
    {
        $query = "SELECT WEEK('2013-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover10() // wendsday start of year after leap year
    {
        $query = "SELECT WEEK('2025-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover11() // thursday start of year after leap year
    {
        $query = "SELECT WEEK('2009-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover12() // friday start of year after leap year
    {
        $query = "SELECT WEEK('1993-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover13() // saturday start of year after leap year
    {
        $query = "SELECT WEEK('2005-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover14() // sunday start of year after leap year
    {
        $query = "SELECT WEEK('2017-01-01', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover15() // sunday end of year
    {
        $query = "SELECT WEEK('2017-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover16() //monday end of year
    {
        $query = "SELECT WEEK('2018-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover17() // tuesday end of year
    {
        $query = "SELECT WEEK('2019-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover18() // wendsday end of year
    {
        $query = "SELECT WEEK('2014-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover19() //thursday end of year
    {
        $query = "SELECT WEEK('2015-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover20() // friday end of year
    {
        $query = "SELECT WEEK('2021-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover21() // saturday end of year
    {
        $query = "SELECT WEEK('2022-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover22() // sunday end of year of leap year
    {
        $query = "SELECT WEEK('2000-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover23() // monday end of year of leap year
    {
        $query = "SELECT WEEK('2012-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover24() // tuesday end of year of leap year
    {
        $query = "SELECT WEEK('2024-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover25() // wendsday end of year of leap year
    {
        $query = "SELECT WEEK('2008-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover26() // thursday end of year of leap year
    {
        $query = "SELECT WEEK('1992-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover27() // friday end of year of leap year
    {
        $query = "SELECT WEEK('2004-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode3Layover28() // saturday end of year of leap year
    {
        $query = "SELECT WEEK('2016-12-31', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }

    public function testWeekMode4Layover() // monday start of year
    {
        $query = "SELECT WEEK('2018-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover2() //tuesday start of year
    {
        $query = "SELECT WEEK('2019-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover3() // wendsday start of year
    {
        $query = "SELECT WEEK('2020-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover4() // thursday start of year
    {
        $query = "SELECT WEEK('2015-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover5() // friday start of year
    {
        $query = "SELECT WEEK('2016-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover6() // saturday start of year
    {
        $query = "SELECT WEEK('2022-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover7() // sunday start of year
    {
        $query = "SELECT WEEK('2023-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover8() // monday start of year after leap year
    {
        $query = "SELECT WEEK('2001-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover9() // tuesday start of year after leap year
    {
        $query = "SELECT WEEK('2013-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover10() // wendsday start of year after leap year
    {
        $query = "SELECT WEEK('2025-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover11() // thursday start of year after leap year
    {
        $query = "SELECT WEEK('2009-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover12() // friday start of year after leap year
    {
        $query = "SELECT WEEK('1993-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover13() // saturday start of year after leap year
    {
        $query = "SELECT WEEK('2005-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 0;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover14() // sunday start of year after leap year
    {
        $query = "SELECT WEEK('2017-01-01', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover15() // sunday end of year
    {
        $query = "SELECT WEEK('2017-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover16() //monday end of year
    {
        $query = "SELECT WEEK('2018-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover17() // tuesday end of year
    {
        $query = "SELECT WEEK('2019-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover18() // wendsday end of year
    {
        $query = "SELECT WEEK('2014-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover19() //thursday end of year
    {
        $query = "SELECT WEEK('2015-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover20() // friday end of year
    {
        $query = "SELECT WEEK('2021-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover21() // saturday end of year
    {
        $query = "SELECT WEEK('2022-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover22() // sunday end of year of leap year
    {
        $query = "SELECT WEEK('2000-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover23() // monday end of year of leap year
    {
        $query = "SELECT WEEK('2012-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover24() // tuesday end of year of leap year
    {
        $query = "SELECT WEEK('2024-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover25() // wendsday end of year of leap year
    {
        $query = "SELECT WEEK('2008-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover26() // thursday end of year of leap year
    {
        $query = "SELECT WEEK('1992-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover27() // friday end of year of leap year
    {
        $query = "SELECT WEEK('2004-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode4Layover28() // saturday end of year of leap year
    {
        $query = "SELECT WEEK('2016-12-31', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }

    // public function testWeekMode5Layover() // monday start of year
    // {
    //     $query = "SELECT WEEK('2018-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover2() //tuesday start of year
    // {
    //     $query = "SELECT WEEK('2019-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover3() // wendsday start of year
    // {
    //     $query = "SELECT WEEK('2020-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover4() // thursday start of year
    // {
    //     $query = "SELECT WEEK('2015-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover5() // friday start of year
    // {
    //     $query = "SELECT WEEK('2016-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover6() // saturday start of year
    // {
    //     $query = "SELECT WEEK('2022-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover7() // sunday start of year
    // {
    //     $query = "SELECT WEEK('2023-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover8() // monday start of year after leap year
    // {
    //     $query = "SELECT WEEK('2001-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 1;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover9() // tuesday start of year after leap year
    // {
    //     $query = "SELECT WEEK('2013-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover10() // wendsday start of year after leap year
    // {
    //     $query = "SELECT WEEK('2025-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover11() // thursday start of year after leap year
    // {
    //     $query = "SELECT WEEK('2009-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover12() // friday start of year after leap year
    // {
    //     $query = "SELECT WEEK('1993-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover13() // saturday start of year after leap year
    // {
    //     $query = "SELECT WEEK('2005-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover14() // sunday start of year after leap year
    // {
    //     $query = "SELECT WEEK('2017-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 0;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover15() // sunday end of year
    // {
    //     $query = "SELECT WEEK('2017-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover16() //monday end of year
    // {
    //     $query = "SELECT WEEK('2018-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 53;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover17() // tuesday end of year
    // {
    //     $query = "SELECT WEEK('2019-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover18() // wendsday end of year
    // {
    //     $query = "SELECT WEEK('2014-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover19() //thursday end of year
    // {
    //     $query = "SELECT WEEK('2015-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover20() // friday end of year
    // {
    //     $query = "SELECT WEEK('2021-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover21() // saturday end of year
    // {
    //     $query = "SELECT WEEK('2022-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover22() // sunday end of year of leap year
    // {
    //     $query = "SELECT WEEK('2000-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover23() // monday end of year of leap year
    // {
    //     $query = "SELECT WEEK('2012-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 53;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover24() // tuesday end of year of leap year
    // {
    //     $query = "SELECT WEEK('2024-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 53;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover25() // wendsday end of year of leap year
    // {
    //     $query = "SELECT WEEK('2008-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover26() // thursday end of year of leap year
    // {
    //     $query = "SELECT WEEK('1992-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover27() // friday end of year of leap year
    // {
    //     $query = "SELECT WEEK('2004-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testWeekMode5Layover28() // saturday end of year of leap year
    // {
    //     $query = "SELECT WEEK('2016-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 52;
    //     $this->assertEquals($expected, $result->value);
    // }

    public function testWeekMode6Layover() // monday start of year
    {
        $query = "SELECT WEEK('2018-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover2() //tuesday start of year
    {
        $query = "SELECT WEEK('2019-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover3() // wendsday start of year
    {
        $query = "SELECT WEEK('2020-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover4() // thursday start of year
    {
        $query = "SELECT WEEK('2015-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover5() // friday start of year
    {
        $query = "SELECT WEEK('2016-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover6() // saturday start of year
    {
        $query = "SELECT WEEK('2022-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover7() // sunday start of year
    {
        $query = "SELECT WEEK('2023-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover8() // monday start of year after leap year
    {
        $query = "SELECT WEEK('2001-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover9() // tuesday start of year after leap year
    {
        $query = "SELECT WEEK('2013-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover10() // wendsday start of year after leap year
    {
        $query = "SELECT WEEK('2025-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover11() // thursday start of year after leap year
    {
        $query = "SELECT WEEK('2009-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover12() // friday start of year after leap year
    {
        $query = "SELECT WEEK('1993-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover13() // saturday start of year after leap year
    {
        $query = "SELECT WEEK('2005-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover14() // sunday start of year after leap year
    {
        $query = "SELECT WEEK('2017-01-01', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover15() // sunday end of year
    {
        $query = "SELECT WEEK('2017-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover16() //monday end of year
    {
        $query = "SELECT WEEK('2018-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover17() // tuesday end of year
    {
        $query = "SELECT WEEK('2019-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover18() // wendsday end of year
    {
        $query = "SELECT WEEK('2014-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover19() //thursday end of year
    {
        $query = "SELECT WEEK('2015-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover20() // friday end of year
    {
        $query = "SELECT WEEK('2021-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover21() // saturday end of year
    {
        $query = "SELECT WEEK('2022-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover22() // sunday end of year of leap year
    {
        $query = "SELECT WEEK('2000-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover23() // monday end of year of leap year
    {
        $query = "SELECT WEEK('2012-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover24() // tuesday end of year of leap year
    {
        $query = "SELECT WEEK('2024-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover25() // wendsday end of year of leap year
    {
        $query = "SELECT WEEK('2008-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover26() // thursday end of year of leap year
    {
        $query = "SELECT WEEK('1992-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover27() // friday end of year of leap year
    {
        $query = "SELECT WEEK('2004-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode6Layover28() // saturday end of year of leap year
    {
        $query = "SELECT WEEK('2016-12-31', 6) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }

    public function testWeekMode7Layover() // monday start of year
    {
        $query = "SELECT WEEK('2018-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover2() //tuesday start of year
    {
        $query = "SELECT WEEK('2019-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover3() // wendsday start of year
    {
        $query = "SELECT WEEK('2020-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover4() // thursday start of year
    {
        $query = "SELECT WEEK('2015-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover5() //SQL logic flaw? says should be 52, but 53 matches all the math = SOLVED??? //friday start of year
    {
        $query = "SELECT WEEK('2016-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover6() // saturday start of year
    {
        $query = "SELECT WEEK('2022-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover7() // sunday start of year
    {
        $query = "SELECT WEEK('2023-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover8() // monday start of year after leap year
    {
        $query = "SELECT WEEK('2001-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover9() // tuesday start of year after leap year
    {
        $query = "SELECT WEEK('2013-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover10() // wendsday start of year after leap year
    {
        $query = "SELECT WEEK('2025-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover11() // thursday start of year after leap year
    {
        $query = "SELECT WEEK('2009-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover12() // friday start of year after leap year
    {
        $query = "SELECT WEEK('1993-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover13() // saturday start of year after leap year
    {
        $query = "SELECT WEEK('2005-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover14() // sunday start of year after leap year
    {
        $query = "SELECT WEEK('2017-01-01', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover15() // sunday end of year
    {
        $query = "SELECT WEEK('2017-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover16() //monday end of year
    {
        $query = "SELECT WEEK('2018-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover17() // tuesday end of year
    {
        $query = "SELECT WEEK('2019-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover18() // wendsday end of year
    {
        $query = "SELECT WEEK('2014-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover19() //thursday end of year
    {
        $query = "SELECT WEEK('2015-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover20() // friday end of year
    {
        $query = "SELECT WEEK('2021-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover21() // saturday end of year
    {
        $query = "SELECT WEEK('2022-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover22() // sunday end of year of leap year
    {
        $query = "SELECT WEEK('2000-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover23() // monday end of year of leap year
    {
        $query = "SELECT WEEK('2012-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover24() // tuesday end of year of leap year
    {
        $query = "SELECT WEEK('2024-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 53;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover25() // wendsday end of year of leap year
    {
        $query = "SELECT WEEK('2008-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover26() // thursday end of year of leap year
    {
        $query = "SELECT WEEK('1992-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover27() // friday end of year of leap year
    {
        $query = "SELECT WEEK('2004-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    public function testWeekMode7Layover28() // saturday end of year of leap year
    {
        $query = "SELECT WEEK('2016-12-31', 7) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 52;
        $this->assertEquals($expected, $result->value);
    }
    
    //endregion

    //region YEARWEEK
    // public function testYearweekNull()
    // {
    //     $query = "SELECT YEARWEEK(NULL, 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $this->assertNull($result->value);
    // }

    // public function testYearweekNoMode()
    // {
    //     $query = "SELECT YEARWEEK(NULL) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $this->assertNull($result->value);
    // }

    // public function testYearweekMode0()
    // {
    //     $query = "SELECT YEARWEEK('2011-10-31', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201144;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode1()
    // {
    //     $query = "SELECT YEARWEEK('2003-03-03', 1) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 200310;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode2()
    // {
    //     $query = "SELECT YEARWEEK('2021-08-16', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 202133;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode3()
    // {
    //     $query = "SELECT YEARWEEK('2000-02-29', 3) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 200009;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode4()
    // {
    //     $query = "SELECT YEARWEEK('2002-04-27', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 200217;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode5()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201853;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode6()
    // {
    //     $query = "SELECT YEARWEEK('1996-06-15', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 199624;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode7()
    // {
    //     $query = "SELECT YEARWEEK('2014-11-21', 7) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201446;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode0Again()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-07', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201901;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode1Again()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-07', 1) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201902;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode2Again()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-07', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201901;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode3Again()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-07', 3) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201902;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode4Again()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-07', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201902;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode5Again()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-07', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201901;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode6Again()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-07', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201902;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode7Again()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-07', 7) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201901;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode0Layover()
    // {
    //     $query = "SELECT YEARWEEK('2017-12-31', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201753;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode0Layover2()
    // {
    //     $query = "SELECT YEARWEEK('2018-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201753;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode0Layover3()
    // {
    //     $query = "SELECT YEARWEEK('2016-12-31', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201652;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode0Layover4()
    // {
    //     $query = "SELECT YEARWEEK('2017-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201701;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode0Layover5()
    // {
    //     $query = "SELECT YEARWEEK('2007-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 200653;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode0Layover6()
    // {
    //     $query = "SELECT YEARWEEK('1987-01-01', 0) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 198652;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode1Layover()
    // {
    //     $query = "SELECT YEARWEEK('2015-12-31', 1) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201553;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode1Layover2()
    // {
    //     $query = "SELECT YEARWEEK('2016-01-01', 1) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201553;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode1Layover3()
    // {
    //     $query = "SELECT YEARWEEK('2011-12-31', 1) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201152;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode1Layover4()
    // {
    //     $query = "SELECT YEARWEEK('2012-01-01', 1) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201152;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode1Layover5()
    // {
    //     $query = "SELECT YEARWEEK('1987-01-01', 1) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 198701;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode2Layover()
    // {
    //     $query = "SELECT YEARWEEK('2018-01-02', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201753;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode2Layover2()
    // {
    //     $query = "SELECT YEARWEEK('2017-12-31', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201753;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode2Layover3()
    // {
    //     $query = "SELECT YEARWEEK('2018-01-07', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201801;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode2Layover4()
    // {
    //     $query = "SELECT YEARWEEK('2016-12-31', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201652;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode2Layover5()
    // {
    //     $query = "SELECT YEARWEEK('2017-01-01', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201701;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode2Layover6()
    // {
    //     $query = "SELECT YEARWEEK('1987-01-01', 2) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 198652;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode3Layover()
    // {
    //     $query = "SELECT YEARWEEK('2016-01-03', 3) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201553;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode3Layover2()
    // {
    //     $query = "SELECT YEARWEEK('2016-01-04', 3) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201601;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode3Layover3()
    // {
    //     $query = "SELECT YEARWEEK('1987-01-01', 3) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 198701;
    //     $this->assertEquals($expected, $result->value);
    // }


    // public function testYearweekMode4Layover()
    // {
    //     $query = "SELECT YEARWEEK('2020-12-31', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 202053;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode4Layover2()
    // {
    //     $query = "SELECT YEARWEEK('2021-01-01', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 202053;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode4Layover3()
    // {
    //     $query = "SELECT YEARWEEK('2019-12-31', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 202001;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode4Layover4()
    // {
    //     $query = "SELECT YEARWEEK('2020-01-01', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 202001;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode4Layover5()
    // {
    //     $query = "SELECT YEARWEEK('2016-12-31', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201652;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode4Layover6()
    // {
    //     $query = "SELECT YEARWEEK('2017-01-01', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201701;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode4Layover7()
    // {
    //     $query = "SELECT YEARWEEK('1987-01-01', 4) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 198653;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode5Layover()
    // {
    //     $query = "SELECT YEARWEEK('2018-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201853;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode5Layover2()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201853;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode5Layover3()
    // {
    //     $query = "SELECT YEARWEEK('2017-12-31', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201752;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode5Layover4()
    // {
    //     $query = "SELECT YEARWEEK('2018-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201801;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode5Layover5()
    // {
    //     $query = "SELECT YEARWEEK('1987-01-01', 5) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 198652;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode6Layover()
    // {
    //     $query = "SELECT YEARWEEK('2014-12-30', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201453;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode6Layover2()
    // {
    //     $query = "SELECT YEARWEEK('2015-01-01', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201453;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode6Layover3()
    // {
    //     $query = "SELECT YEARWEEK('2014-01-01', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201401;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode6Layover4()
    // {
    //     $query = "SELECT YEARWEEK('2013-12-31', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201401;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode6Layover5()
    // {
    //     $query = "SELECT YEARWEEK('2012-01-01', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201201;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode6Layover6()
    // {
    //     $query = "SELECT YEARWEEK('2011-12-31', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201152;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode6Layover7()
    // {
    //     $query = "SELECT YEARWEEK('1987-01-01', 6) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 198653;
    //     $this->assertEquals($expected, $result->value);
    // }

    // public function testYearweekMode7Layover()
    // {
    //     $query = "SELECT YEARWEEK('2019-01-01', 7) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201853;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode7Layover2()
    // {
    //     $query = "SELECT YEARWEEK('2018-12-30', 7) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201852;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode7Layover3()
    // {
    //     $query = "SELECT YEARWEEK('2017-12-31', 7) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201752;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode7Layover4()
    // {
    //     $query = "SELECT YEARWEEK('2018-01-01', 7) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201801;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode7Layover5() //SQL logic flaw? says should be 201552, but 201553 matches all the math = SOLVED???
    // {
    //     $query = "SELECT YEARWEEK('2016-01-01', 7) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 201552;
    //     $this->assertEquals($expected, $result->value);
    // }
    // public function testYearweekMode7Layover6()
    // {
    //     $query = "SELECT YEARWEEK('1987-01-01', 7) as value;";
    //     $result = $this->conn->selectOne($query);
    //     $expected = 198652;
    //     $this->assertEquals($expected, $result->value);
    // }
    
    //endregion
}
