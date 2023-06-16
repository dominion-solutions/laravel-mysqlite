<?php

namespace Mhorninger;

use Mhorninger\SQLite\MySQLiteConnection;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected ?MySQLiteConnection $conn = null;

    public function setUp(): void
    {
        //The PDO is not necessary to have right now, so we're not going to define it.
        $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        //Set up the connection.
        $this->conn = new MySQLiteConnection($pdo);
    }

    public function selectValue(string $query)
    {
        return collect($this->conn->select($query)[0])->values()->first();
    }
}
