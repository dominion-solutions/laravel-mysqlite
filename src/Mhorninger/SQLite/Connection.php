<?php
namespace Mhorninger\SQLite;

use Mhorninger\MySQLite\MySQLite;

class Connection extends \Illuminate\Database\SQLiteConnection
{
     /**
     * Create a new database connection instance.
     *
     * @param  \PDO|\Closure     $pdo
     * @param  string   $database
     * @param  string   $tablePrefix
     * @param  array    $config
     * @return void
     */
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
        $this->pdo = MySQLite::createFunctions($this->pdo);
    }
}
