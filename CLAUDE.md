# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**concept7/laravel-kite** is a Laravel package that reports project metadata (PHP version, database version, framework versions, frontend tooling, installed packages) to the Kite monitoring API on a daily schedule.

This is part of a three-project ecosystem in the `Monitor/` directory:

- **kite-backend** (`../kite-backend`): Laravel 12 + Filament admin panel that receives and displays project data. Projects authenticate via Sanctum API tokens. API endpoint: `POST /api/project/{project:uuid}`.
- **concept7/kite** (`../kite`): Framework-agnostic core PHP package containing the pipeline, actions, HTTP client, and config. Linked via Composer path repository.
- **concept7/laravel-kite** (this repo): Thin Laravel adapter adding Laravel-specific actions and project info collection.

## Commands

```bash
composer test              # Run tests (Pest v4)
composer test -- --filter=TestName  # Run a single test
composer format            # Format code (Laravel Pint)
composer analyse           # Static analysis (PHPStan)
```

## Architecture

### Two-Package Design

- **concept7/kite** (core): Framework-agnostic pipeline, actions, HTTP client, config, Composer dependency collection.
- **concept7/laravel-kite** (this repo): Laravel service provider, artisan command, Laravel-ecosystem actions, project info collector.

### Reporting Flow

`LaravelKiteReportCommand` builds a `KiteConfig` (uri, projectId, projectKey) from env vars → creates `Kite::make($config)` → adds Laravel-specific actions from config → runs pipeline → filters empty values → collects project info via `LaravelProjectInfoCollector` (which uses `ComposerDependencies::direct()` from the core package) → POSTs to Kite API with bearer token auth → returns `ReportResult`.

### Key Patterns

- **Pipeline pattern**: Actions implement `ActionInterface` (`handle(Collection $data, Closure $next)`), chained via `Illuminate\Pipeline`.
- **Base action classes** in core for reuse: `GetComposerPackageVersionAction` (checks `Composer\InstalledVersions`) and `GetNodePackageVersionAction` (reads `package-lock.json`, auto-detects project root). Laravel-specific actions extend these.
- **Shared utilities** in core: `ComposerDependencies::direct()` collects direct Composer dependencies via `composer show`.
- **Value objects**: `KiteConfig` (uri, projectId, projectKey — immutable with validation), `ReportResult` (static constructors `success()`/`failure()`).
- **Fluent API**: `Kite::make($config)->projectInfoCollector(...)->addAction(...)->report()`.

### Key Files

- `src/Commands/LaravelKiteReportCommand.php` — Artisan command, main entry point
- `src/ProjectInfo/LaravelProjectInfoCollector.php` — Collects Laravel environment info and Composer dependencies
- `src/Actions/` — Laravel-ecosystem actions (Laravel, Statamic, Livewire, Filament, Vite)
- `config/kite.php` — Configuration with action classes list
- `src/LaravelKiteServiceProvider.php` — Registers config, command, and daily schedule

### Testing

- **Pest v4** with **Orchestra Testbench** for Laravel integration testing
- Feature tests use `TestCase` (extends `Orchestra\Testbench\TestCase`) which auto-registers the service provider
- Unit tests cover actions, config validation, pipeline processing, and the reporter

### Environment Variables

- `KITE_URI` — Base URL of Kite API (required)
- `KITE_PROJECT_ID` — Project identifier (required)
- `KITE_PROJECT_KEY` — API auth key (required)
- `KITE_PHP_PATH` — PHP binary path (optional, default: `php`)

## Conventions

- PHP 8.2+ with readonly properties and named arguments
- Supports Laravel 10, 11, and 12
- Missing packages/tools are silently skipped (no exceptions)
- Namespace: `Concept7\LaravelKite` (this package), `Concept7\Kite` (core package)
