<?php
namespace Mhorninger\MySQLite;

class StringMethodTest extends \Mhorninger\TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

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

    public function testLpadNull()
    {
        $query = "SELECT LPAD(NULL,1,'??') as value;";
        $result = $this->conn->selectOne($query);
        $this->assertNull($result->value);
    }
}
