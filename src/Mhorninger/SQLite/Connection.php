<?php
namespace Mhorninger\SQLite;

use \ReflectionClass;
use Mhorninger\MySQLite\MySQLite;
use Mhorninger\MySQLite\Constants;

class Connection extends \Illuminate\Database\SQLiteConnection
{
    const ESCAPE_CHARS = ['`', '[', '"'];
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

    /**
     * Run a select statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return array
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $query = $this->scanQueryForConstants($query);
        return $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }
            // For select statements, we'll simply execute the query and return an array
            // of the database result set. Each element in the array will be a single
            // row from the database table, and will either be an array or objects.
            $statement = $this->prepared($this->getPdoForSelect($useReadPdo)
                              ->prepare($query));
            
            $this->bindValues($statement, $this->prepareBindings($bindings));

            $statement->execute();

            return $statement->fetchAll();
        });
    }

    private function scanQueryForConstants($query)
    {
        $reflection = new ReflectionClass(Constants::class);
        $constants = $reflection->getConstants();
        $placeholders = array_keys($constants);
        foreach ($placeholders as $placeholder) {
            $query = str_replace($placeholder, "'" . $constants[$placeholder] . "'", $query);
        }
        return $query;
    }
}
