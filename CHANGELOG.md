# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- **[BREAKING]** Minimum PHP version raised from 7.3 to 8.2.
- **[BREAKING]** Minimum Laravel version raised from 7.x/8.x to 12.x/13.x (`illuminate/database ^12.0 || ^13.0`).
- CI test matrix now targets PHP 8.2, 8.3, and 8.4.

### Added

- Support for Laravel 12.x and 13.x.
- This changelog to track project changes going forward.
- `UPGRADING.md` with migration instructions from v1.x to v2.0.
- Requirements section in `README.md` documenting supported PHP and Laravel versions.

### Removed

- Support for PHP 7.3, 7.4, 8.0, and 8.1.
- Support for Laravel 7.x (`illuminate/database ^7.0`).
- Support for Laravel 8.x (`illuminate/database ^8.0`).
