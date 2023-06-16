<?php

namespace Mhorninger\MySQLite;

use Carbon\CarbonImmutable;
use Mhorninger\TestCase;

class MiscellaneousMethodTest extends TestCase
{
    public function testInetNtoa()
    {
        $query = 'SELECT INET_NTOA(167773449) as value;';
        $result = $this->conn->selectOne($query);
        $expected = '10.0.5.9';
        $this->assertEquals($expected, $result->value);
    }

    public function testInetNtoaNull()
    {
        $query = 'SELECT INET_NTOA(NULL) as value;';
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    public function testAddFunction()
    {
        $date = CarbonImmutable::today();

        $this->conn->addFunction('TEST_DATE', fn() => $date, 0);

        $result = $this->selectValue('SELECT TEST_DATE()');
        $this->assertEquals($date, $result);
    }

    public function testAddRewriteRule()
    {
        $date = CarbonImmutable::today();

        $this->conn->addRewriteRule('/TEST_DATE\(\)/', "date('now')");

        $result = $this->selectValue('SELECT TEST_DATE()');
        $this->assertEquals($date->toDateString(), $result);
    }
}
