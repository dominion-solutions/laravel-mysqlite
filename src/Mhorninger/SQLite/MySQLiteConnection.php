<?php

namespace Mhorninger\SQLite;

use Mhorninger\MySQLite\MethodRewriteConstants;
use Mhorninger\MySQLite\MySQLite;
use Mhorninger\MySQLite\SubstitutionConstants;
use Mhorninger\MySQLite\UnquotedSubstitutionConstants;
use ReflectionClass;

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
        //Skip on inserts.
        $insertRegex = '/INSERT INTO.*?;/';
        if (0 == preg_match($insertRegex, $query)) {
            $query = $this->methodRewrite($query);
            $query = $this->scanQueryForConstants($query);
        }

        return parent::run($query, $bindings, $callback);
    }

    private function scanQueryForConstants($query)
    {
        $reflection = new ReflectionClass(SubstitutionConstants::class);
        $constants = $reflection->getConstants();
        $placeholders = array_keys($constants);
        foreach ($placeholders as $placeholder) {
            $searchFor = '/'.preg_quote($placeholder).'(?!\\(|\\w|\\))/';
            $query = preg_replace($searchFor, "'".$constants[$placeholder]."'", $query);
        }
        $reflection = new ReflectionClass(UnquotedSubstitutionConstants::class);
        $methodConstants = $reflection->getConstants();
        $placeholders = array_keys($methodConstants);
        foreach ($placeholders as $placeholder) {
            $query = str_replace($placeholder, $methodConstants[$placeholder], $query);
        }

        return $query;
    }

    private function methodRewrite($query)
    {
        foreach (array_keys(MethodRewriteConstants::METHOD_REPLACEMENTS) as $regex) {
            $query = preg_replace($regex, MethodRewriteConstants::METHOD_REPLACEMENTS[$regex], $query);
        }

        return $query;
    }
}
