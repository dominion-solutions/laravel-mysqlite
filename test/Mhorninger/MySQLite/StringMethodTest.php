<?php

namespace Mhorninger\MySQLite;

class StringMethodTest extends \Mhorninger\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    //region FORMAT tests
    public function testFormatTruncateDecimal()
    {
        $query = 'SELECT FORMAT(12332.123456, 4) as value;';
        $result = $this->conn->selectOne($query);
        $expected = '12,332.1235';
        $this->assertEquals($expected, $result->value);
    }

    public function testFormatAppendDecimal()
    {
        $query = 'SELECT FORMAT(12332.1,4) as value;';
        $result = $this->conn->selectOne($query);
        $expected = '12,332.1000';
        $this->assertEquals($expected, $result->value);
    }

    public function testFormatWholeOnly()
    {
        $query = 'SELECT FORMAT(12332.2,0) as value;';
        $result = $this->conn->selectOne($query);
        $expected = '12,332';
        $this->assertEquals($expected, $result->value);
    }

    public function testFormatWithInternationalization()
    {
        $query = "SELECT FORMAT(12332.2,2,'de_DE') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '12.332,20';
        $this->assertEquals($expected, $result->value);
    }

    public function testFormatNull()
    {
        $query = 'SELECT FORMAT(null,2) as value;';
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    public function testFormatWithMathProblem()
    {
        $query = 'SELECT FORMAT((3600 * 1.7 / 281), 1) as value';
        $result = $this->conn->selectOne($query);
        $expected = '21.8';
        $this->assertEquals($expected, $result->value);
    }

    //endregion

    //region LPAD tests
    public function testLpad()
    {
        $query = "SELECT LPAD('hi',4,'??') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '??hi';
        $this->assertEquals($expected, $result->value);
    }

    public function testLpadShorten()
    {
        $query = "SELECT LPAD('hi',1,'??') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 'h';
        $this->assertEquals($expected, $result->value);
    }

    public function testLpadZero()
    {
        $query = "SELECT LPAD('0',4,'0') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '0000';
        $this->assertEquals($expected, $result->value);
    }

    public function testLpadNull()
    {
        $query = "SELECT LPAD(NULL,1,'??') as value;";
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    //endregion

    //region RPAD tests
    public function testRpad()
    {
        $query = "SELECT RPAD('hi',4,'??') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 'hi??';
        $this->assertEquals($expected, $result->value);
    }

    public function testRpadShorten()
    {
        $query = "SELECT RPAD('hi',1,'??') as value;";
        $result = $this->conn->selectOne($query);
        $expected = 'h';
        $this->assertEquals($expected, $result->value);
    }

    public function testRpadZero()
    {
        $query = "SELECT RPAD('0',4,'0') as value;";
        $result = $this->conn->selectOne($query);
        $expected = '0000';
        $this->assertEquals($expected, $result->value);
    }

    public function testRpadNull()
    {
        $query = "SELECT RPAD(NULL,1,'??') as value;";
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }

    public function testLeft()
    {
        $query = "SELECT LEFT('TESTING', 4) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 'TEST';
        $this->assertEquals($expected, $result->value);
    }

    public function testRight()
    {
        $query = "SELECT RIGHT('TESTING', 3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 'ING';
        $this->assertEquals($expected, $result->value);
    }

    //endregion
}
