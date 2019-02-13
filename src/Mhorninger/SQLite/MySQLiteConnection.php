<?php

namespace Mhorninger\SQLite;

use ReflectionClass;
use Mhorninger\MySQLite\MySQLite;
use Mhorninger\MySQLite\SubstitutionConstants;
use Mhorninger\MySQLite\MethodSubstitutionConstants;

class MySQLiteConnection extends \Illuminate\Database\SQLiteConnection
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
        //Make sure the PDO is actually a PDO and not a closure.
        $this->pdo = $this->getPdo();
        $this->pdo = MySQLite::createFunctions($this->pdo);
    }

    public function run($query, $bindings, \Closure $callback)
    {
        $query = $this->scanQueryForConstants($query);

        return parent::run($query, $bindings, $callback);
    }

    private function scanQueryForConstants($query)
    {
        $reflection = new ReflectionClass(SubstitutionConstants::class);
        $constants = $reflection->getConstants();
        $placeholders = array_keys($constants);
        foreach ($placeholders as $placeholder) {
            $searchFor = '/'.preg_quote($placeholder).'(?!\\(|\\w)/';
            $query = preg_replace($searchFor, "'".$constants[$placeholder]."'", $query);
        }
        $reflection = new ReflectionClass(MethodSubstitutionConstants::class);
        $methodConstants = $reflection->getConstants();
        $placeholders = array_keys($methodConstants);
        foreach ($placeholders as $placeholder) {
            $query = str_replace($placeholder, $methodConstants[$placeholder], $query);
        }

        return $query;
    }
}
