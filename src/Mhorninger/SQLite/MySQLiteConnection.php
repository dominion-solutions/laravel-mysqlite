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
    public const ESCAPE_CHARS = ['`', '[', '"'];

    private Collection $rewriteRules;

    /**
     * Create a new database connection instance.
     *
     * @param  \PDO|\Closure  $pdo
     * @param  string  $database
     * @param  string  $tablePrefix
     * @param  array  $config
     */
    public function __construct(\PDO|\Closure $pdo, string $database = '', string $tablePrefix = '', array $config = [])
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
        if (preg_match($insertRegex, $query) === 0) {
            $query = $this->methodRewrite($query);
            $query = $this->scanQueryForConstants($query);
        }

        return parent::run($query, $bindings, $callback);
    }

    private function scanQueryForConstants(string $query): string
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

    public function addRewriteRule(string $regex, string $replacement): self
    {
        $this->rewriteRules->push([$regex, $replacement]);

        return $this;
    }

    private function methodRewrite(string $query): string
    {
        return $this
            ->rewriteRules
            ->reduce(fn (string $query, array $rule): string => preg_replace($rule[0], $rule[1], $query), $query);
    }
}
