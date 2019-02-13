<?php

namespace Mhorninger\MySQLite;

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
}
