<?php

namespace Mhorninger;

use PDO;
use Mhorninger\SQLite\MySQLiteConnection;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $conn = null;

    protected function setUp() : void
    {
        //The PDO is not necessary to have right now, so we're not going to define it.
        $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        //Set up the connection.
        $this->conn = new MySQLiteConnection($pdo);
    }
}
