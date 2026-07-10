# AGENTS.md

## Project Overview

`spam-n-eggs/laravel-mysqlite` is a Laravel database connection package that adds MySQL-compatible SQL functions to SQLite. It extends `Illuminate\Database\SQLiteConnection` to intercept queries and rewrite/translate MySQL syntax into SQLite equivalents. It wraps the `vectorface/mysqlite` library and integrates it into Laravel's database layer via a ServiceProvider.

## Critical Rules

### Backward Compatibility is Mandatory

- **Never remove or break existing functionality** unless the targeted version constraint has been explicitly removed from `composer.json`.
- If `illuminate/database` support for a Laravel version is dropped (e.g., removing `^7.0`), that is the only time breaking changes tied to that version are acceptable.
- All existing ported MySQL functions, rewrite rules, constants, and the public API (`addFunction()`, `addRewriteRule()`) must continue to work exactly as documented in `README.md`.
- New MySQL function ports must be additive only. Never alter the behavior or return type of an existing ported function.

### Do Things the Laravel Way

This package lives inside the Laravel ecosystem. All code must follow Laravel conventions:

- **Service Providers**: Registration, binding, and boot logic must follow the patterns used by `illuminate/*` packages. The `MySQLiteServiceProvider` extends `ServiceProvider` and must remain compatible with Laravel's container resolution.
- **Connection resolution**: The package overrides the `sqlite` connection resolver. This must work transparently — users should not need to change their `config/database.php` beyond what the README describes.
- **PSR-4 autoloading**: Namespaces follow `Mhorninger\` mapped to `src/Mhorninger/`. New classes must be placed in the correct namespace directory.
- **Use Illuminate components**: When interacting with database layer internals, use `illuminate/database` APIs (e.g., `Connection`, `SQLiteConnection`) rather than raw PDO wherever possible.
- **Carbon for dates**: Date/time handling must use Carbon/CarbonImmutable, consistent with Laravel's conventions. Do not use raw PHP `DateTime`.

### Code Style

- **PSR-2** is the baseline standard (enforced via PHP_CodeSniffer and StyleCI with the `laravel` preset).
- Run `./vendor/bin/phpcs` before submitting changes. The config is in `phpcs.xml`.
- No line length limit is enforced, but keep lines reasonable.
- UpperCase constant naming and no unnecessary string concatenation are enforced.

## Development Commands

```bash
# Run the full test suite
./vendor/bin/phpunit

# Check code style
./vendor/bin/phpcs

# Static analysis (PHPStan — config in phpstan.neon, baseline in phpstan-baseline.neon)
./vendor/bin/phpstan analyse
```

## Testing

- **Framework**: PHPUnit 9.5, configured in `phpunit.xml`.
- **Bootstrap**: `test/bootstrap.php` (handles autoloading manually — there is no `autoload-dev` in `composer.json`).
- **Base class**: `Mhorninger\TestCase` — creates an in-memory SQLite PDO and wraps it in `MySQLiteConnection`.
- **Pattern**: Tests execute actual SQL queries against an in-memory SQLite database with MySQLite functions registered. Assert results using the `selectValue()` helper from `TestCase`.
- **Coverage**: `phpunit.xml` generates Clover XML to `test/_reports/cov.xml`. Coverage must not drop below the master branch threshold (reported via Coveralls).
- **File naming**: Test classes are `*Test.php` in `test/`. One test class per logical unit of functionality (e.g., `DateMethodTest`, `StringMethodTest`).

## Architecture

### Key Classes

| Class | Path | Role |
|-------|------|------|
| `MySQLiteServiceProvider` | `src/Mhorninger/SQLite/MySQLiteServiceProvider.php` | Registers the custom `sqlite` connection resolver |
| `MySQLiteConnection` | `src/Mhorninger/SQLite/MySQLiteConnection.php` | Extends `SQLiteConnection`, rewrites queries in `run()` |
| `MySQLite` | `src/Mhorninger/MySQLite/MySQLite.php` | Extends Vectorface MySQLite, registers PDO functions |

### Extension Points

- **`addFunction(name, callable, argc)`**: Register a custom SQLite function via `PDO::sqliteCreateFunction()`. Used on `MySQLiteConnection`.
- **`addRewriteRule(pattern, replacement)`**: Add a regex-based query rewrite rule. Used on `MySQLiteConnection`.

### Adding a New MySQL Function Port

1. Add the implementation as a `mysql_*` static method on the appropriate trait in `src/Mhorninger/MySQLite/MySQL/` (create a new trait if none of the existing ones — `DateTimeExtended`, `StringExtended`, `NumericExtended`, `Miscellaneous` — fit).
2. The `MySQLite` core class uses reflection to discover all `mysql_*` methods across its traits and auto-registers them as PDO functions.
3. If the function requires query rewriting (not just a PDO function call), add a regex rule to `MethodRewriteConstants`.
4. If the function requires string substitutions in the query, add to `SubstitutionConstants` or `UnquotedSubstitutionConstants` as appropriate.
5. Write exhaustive tests in `test/Mhorninger/MySQLite/` following the pattern of existing test classes.

### Supported Versions

- **PHP**: >= 7.3.0
- **illuminate/database**: ^7.0 || ^8.0
- **vectorface/mysqlite**: ^0.1.4

When expanding version support, ensure tests pass against the new versions and update `composer.json` constraints accordingly.

## Commits and PRs

- Tag commits with issue numbers when resolving issues: `#N description`
- PRs must pass all tests and maintain or improve code coverage
- Reference related issues in PR descriptions
- Include steps to test changes in PR descriptions
- All contributions must follow the Code of Conduct in `.github/CODE_OF_CONDUCT.md`
