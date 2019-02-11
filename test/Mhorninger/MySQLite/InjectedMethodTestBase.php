<?php

namespace Mhorninger\MySQLite;

use PDO;

class InjectedMethodTestBase extends TestCase
{
    protected $conn = null;

    public function setUp()
    {
        //The PDO is not necessary to have right now, so we're not going to define it.
        $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        //Set up the connection.
        $this->conn = new MySQLiteConnection($pdo);
    }
}
