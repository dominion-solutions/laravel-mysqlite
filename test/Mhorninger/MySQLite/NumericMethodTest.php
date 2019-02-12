<?php
namespace Mhorninger\MySQLite;

use Mhorninger\TestCase;

class NumericMethodTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    #region MOD
    public function testModFunctionTens()
    {
        $query = "SELECT MOD(234, 10) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 4;
        $this->assertEquals($expected, $result->value);
    }

    public function testModFunctionWithPercentModulo()
    {
        $query = "SELECT 253 % 7 as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1;
        $this->assertEquals($expected, $result->value);
    }

    public function testModFunctionOnes()
    {
        $query = "SELECT MOD(29,9) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 2;
        $this->assertEquals($expected, $result->value);
    }

    public function testModFunctionDecimal()
    {
        $query = "SELECT MOD(34.5,3) as value;";
        $result = $this->conn->selectOne($query);
        $expected = 1.5;
        $this->assertEquals($expected, $result->value);
    }

    public function testModFunctionNull()
    {
        $query = "SELECT MOD(NULL,3) as value;";
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }
    #endregion
}
