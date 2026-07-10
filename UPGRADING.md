# Upgrading from v1.x to v2.0

Version 2.0 of `spam-n-eggs/laravel-mysqlite` is a major release that drops support for older PHP and Laravel versions. This guide will help you upgrade from any v1.x release to v2.0.

## Requirements

| Dependency | v1.x | v2.0 |
|------------|------|------|
| PHP | >= 7.3 | >= 8.2 |
| Laravel | 7.x or 8.x | 12.x or 13.x |
| `illuminate/database` | `^7.0 \|\| ^8.0` | `^12.0 \|\| ^13.0` |

## Step 1: Upgrade PHP

Before updating the package, ensure your server is running **PHP 8.2 or higher**.

Check your current PHP version:

```bash
php -v
```

If you are running PHP < 8.2, you must upgrade PHP first. Refer to your hosting provider's documentation or use a version manager like [phpbrew](https://phpbrew.github.io/phpbrew/) or [asdf](https://asdf-vm.com/) to switch versions.

## Step 2: Upgrade Laravel

This package requires **Laravel 12.x or 13.x**. If your application is running Laravel 7.x or 8.x, you must upgrade Laravel first.

Follow the official Laravel upgrade guides:

- [Laravel 7.x → 8.x](https://laravel.com/docs/8.x/upgrade)
- [Laravel 8.x → 9.x](https://laravel.com/docs/9.x/upgrade)
- [Laravel 9.x → 10.x](https://laravel.com/docs/10.x/upgrade)
- [Laravel 10.x → 11.x](https://laravel.com/docs/11.x/upgrade)
- [Laravel 11.x → 12.x](https://laravel.com/docs/12.x/upgrade)

> **Note:** Each major Laravel upgrade may have its own breaking changes. Work through each version incrementally if you are upgrading across multiple major versions.

## Step 3: Update the Package

Once PHP 8.2+ and Laravel 12.x+ are running, update the package:

```bash
composer require spam-n-eggs/laravel-mysqlite:^2.0
```

## Step 4: Verify

Run your test suite to confirm everything works:

```bash
./vendor/bin/phpunit
```

## Troubleshooting

### Composer dependency resolution errors

If Composer fails to resolve dependencies, ensure no other packages in your project still require `illuminate/database ^7.0` or `^8.0`. Run `composer why illuminate/database` to inspect the dependency tree.

### Function or class not found errors

The public API (`addFunction()`, `addRewriteRule()`, all ported MySQL functions) remains unchanged in v2.0. If you encounter errors, verify that:

1. The `MySQLiteServiceProvider` is registered in your application.
2. Your `config/database.php` SQLite connection uses the `mysqlite` driver as documented in `README.md`.
3. You are running the correct PHP and Laravel versions.

### PHPUnit compatibility

v2.0 continues to use PHPUnit 9.5. No changes to your test setup should be required.
