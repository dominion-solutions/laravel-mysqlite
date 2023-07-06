<?php

namespace Mhorninger\SQLite;

use Illuminate\Support\Collection;
use Mhorninger\MySQLite\MethodRewriteConstants;
use Mhorninger\MySQLite\MySQLite;
use Mhorninger\MySQLite\SubstitutionConstants;
use Mhorninger\MySQLite\UnquotedSubstitutionConstants;
use ReflectionClass;

class MySQLiteConnection extends \Illuminate\Database\SQLiteConnection
{
    const ESCAPE_CHARS = ['`', '[', '"'];
    private Collection $rewriteRules;

    /**
     * Create a new database connection instance.
     *
     * @param  \PDO|\Closure  $pdo
     * @param  string  $database
     * @param  string  $tablePrefix
     * @param  array  $config
     * @return void
     */
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);

        $this->rewriteRules = collect(MethodRewriteConstants::METHOD_REPLACEMENTS);

        //Make sure the PDO is actually a PDO and not a closure.
        $this->pdo = $this->getPdo();
        $this->pdo = MySQLite::createFunctions($this->pdo);
    }

    public function run($query, $bindings, \Closure $callback)
    {
        // Skip on inserts.
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

    public function addFunction(string $function, callable $callback, int $paramCount): self
    {
        $this->pdo->sqliteCreateFunction($function, $callback, $paramCount);

        return $this;
    }

    public function addRewriteRule($regex, $replacement): self
    {
        $this->rewriteRules->push([$regex, $replacement]);

        return $this;
    }

    private function methodRewrite($query)
    {
        return $this
            ->rewriteRules
            ->reduce(fn ($query, $rule) => preg_replace($rule[0], $rule[1], $query), $query);
    }
}
