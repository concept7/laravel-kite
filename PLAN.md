# Plan: Extract `concept7/kite` Core PHP Package from `concept7/laravel-kite`

## Context

`concept7/laravel-kite` is a Laravel package that collects environment and dependency information (PHP version, database version, framework versions, frontend tool versions) and reports it to a remote Kite monitoring API. All logic is currently coupled to Laravel (Pipeline, Collection, Http facade, Process facade, helpers like `blank()`, `base_path()`, `data_get()`).

The goal is to extract a framework-agnostic PHP package (`concept7/kite`) containing the core pipeline, actions, HTTP client, and config — so it can be reused in non-Laravel projects. The Laravel package becomes a thin adapter that adds Laravel-ecosystem-specific actions and project info collection.

---

## Phase 1: Create `concept7/kite` Core Package

### 1.1 Directory Structure

```
kite/
├── composer.json
├── LICENSE.md
├── src/
│   ├── KiteReporter.php                          # Orchestrator: pipeline + HTTP reporting
│   ├── KiteConfig.php                            # Value object for all configuration
│   ├── Pipeline.php                              # Framework-agnostic pipeline (replaces Illuminate\Pipeline)
│   ├── ReportResult.php                          # Value object for report success/failure
│   ├── Contracts/
│   │   ├── ActionInterface.php                   # Contract for pipeline actions
│   │   └── ProjectInfoCollectorInterface.php     # Contract for framework-specific project info
│   ├── Actions/
│   │   ├── GetPhpVersionAction.php               # phpversion()
│   │   ├── GetMysqlVersionAction.php             # shell_exec('mysql --version')
│   │   ├── GetComposerPackageVersionAction.php   # Generic: checks Composer\InstalledVersions
│   │   ├── GetNodePackageVersionAction.php       # Generic: reads package-lock.json
│   │   ├── GetTailwindVersionAction.php          # Extends GetNodePackageVersionAction (tailwindcss)
│   │   └── GetViteVersionAction.php              # Extends GetNodePackageVersionAction (vite)
│   ├── Http/
│   │   └── KiteHttpClient.php                    # Guzzle-based HTTP client
│   └── Support/
│       └── Helpers.php                           # blank(), filled(), dataGet()
└── tests/
```

### 1.2 `composer.json`

```json
{
    "name": "concept7/kite",
    "description": "Framework-agnostic Kite monitoring client for PHP projects",
    "license": "MIT",
    "require": {
        "php": "^8.2",
        "composer-runtime-api": "^2.0",
        "guzzlehttp/guzzle": "^7.0"
    },
    "autoload": {
        "psr-4": { "Concept7\\Kite\\": "src/" }
    }
}
```

Note: `composer-runtime-api` (virtual package) replaces `composer/composer` — gives access to `Composer\InstalledVersions` without the full Composer dependency.

### 1.3 Contracts

**`ActionInterface`** — replaces the implicit pipeline contract. Uses `array` instead of `Collection`:

```php
interface ActionInterface {
    public function handle(array $data, Closure $next): array;
}
```

**`ProjectInfoCollectorInterface`** — each framework implements this:

```php
interface ProjectInfoCollectorInterface {
    public function collect(): array;
}
```

### 1.4 Key Classes

**`KiteConfig`** — value object holding URI, project ID/key, project root, PHP path, and optional actions list. Has `isValid()` and `apiUrl()` methods.

**`Pipeline`** — pure PHP replacement for `Illuminate\Pipeline`. Uses `array_reduce` to build the middleware chain. Accepts both class strings (instantiated via `new`) and pre-built instances. Same API: `->send([])->through($actions)->thenReturn()`.

**`KiteReporter`** — the main orchestrator. Receives `KiteConfig` and optional `ProjectInfoCollectorInterface`. Runs the pipeline, filters empty values, collects project info, sends via `KiteHttpClient`. Has `defaultActions()` returning the standard core set and `addAction()` for framework-specific extras.

**`KiteHttpClient`** — wraps Guzzle. Sends POST with bearer token. Returns `ReportResult`.

**`ReportResult`** — value object with `bool $success`, `string $message`, `int $statusCode`. Static constructors `success()` and `failure()`.

**`Helpers`** — static methods `blank()`, `filled()`, `dataGet()` replacing Laravel helpers.

### 1.5 Actions — What Goes Where

| Action | Core? | Laravel? | Notes |
|--------|:---:|:---:|-------|
| `GetPhpVersionAction` | **Yes** | | `phpversion()` — zero deps |
| `GetMysqlVersionAction` | **Yes** | | `shell_exec('mysql --version')` replaces Process facade |
| `GetTailwindVersionAction` | **Yes** | | Extends `GetNodePackageVersionAction`, receives `$projectRoot` |
| `GetViteVersionAction` | **Yes** | | Extends `GetNodePackageVersionAction`, receives `$projectRoot` |
| `GetLaravelVersionAction` | | **Yes** | Uses `app()->version()` |
| `GetStatamicVersionAction` | | **Yes** | Laravel ecosystem — extends `GetComposerPackageVersionAction` from core |
| `GetLivewireVersionAction` | | **Yes** | Laravel ecosystem — extends `GetComposerPackageVersionAction` from core |
| `GetFilamentVersionAction` | | **Yes** | Laravel ecosystem — extends `GetComposerPackageVersionAction` from core |
| `GetProjectInformationAction` | | **Yes** | Refactored into `LaravelProjectInfoCollector` |

**Generic base classes** in core (for reuse by framework adapters):
- `GetComposerPackageVersionAction` — takes `$metaKey` + `$packages[]`, tries each via `InstalledVersions::getVersion()`. Statamic/Livewire/Filament actions in laravel-kite extend this.
- `GetNodePackageVersionAction` — takes `$projectRoot`, `$metaKey`, `$nodePackageName`, reads `package-lock.json`. Tailwind/Vite actions extend this.

---

## Phase 2: Refactor `concept7/laravel-kite`

### 2.1 Updated `composer.json`

- Add `"concept7/kite": "^1.0"` to require
- Remove `"composer/composer": "^2.9"` (comes transitively)

### 2.2 Updated File Structure

```
laravel-kite/
├── composer.json
├── config/kite.php                                    # Updated action class references
├── src/
│   ├── LaravelKiteServiceProvider.php                 # Unchanged
│   ├── LaravelKite.php                                # Unchanged
│   ├── Facades/LaravelKite.php                        # Unchanged
│   ├── Actions/
│   │   ├── GetLaravelVersionAction.php                # Implements core ActionInterface
│   │   ├── GetStatamicVersionAction.php               # Extends core GetComposerPackageVersionAction
│   │   ├── GetLivewireVersionAction.php               # Extends core GetComposerPackageVersionAction
│   │   └── GetFilamentVersionAction.php               # Extends core GetComposerPackageVersionAction
│   ├── ProjectInfo/
│   │   └── LaravelProjectInfoCollector.php            # NEW: implements ProjectInfoCollectorInterface
│   └── Commands/
│       └── LaravelKiteReportCommand.php               # Delegates to KiteReporter
```

**Deleted files** (moved to core): `GetPhpVersionAction`, `GetMysqlVersionAction`, `GetTailwindVersion`, `GetViteVersionAction`, `GetProjectInformationAction`.

**Refactored files** (now extend core base classes): `GetStatamicVersionAction`, `GetLivewireVersionAction`, `GetFilamentVersionAction` — each becomes a one-liner extending `Concept7\Kite\Actions\GetComposerPackageVersionAction` with the appropriate package name(s).

### 2.3 Refactored Command

The command becomes thin — builds `KiteConfig` from Laravel config, instantiates `LaravelProjectInfoCollector`, delegates to `KiteReporter`:

```php
public function handle(): int
{
    $config = new KiteConfig(
        uri: config('kite.uri', ''),
        projectId: config('kite.project_id', ''),
        projectKey: config('kite.project_key', ''),
        projectRoot: base_path(),
        phpPath: config('kite.php_path', 'php'),
    );

    $collector = new LaravelProjectInfoCollector(config('kite.php_path', 'php'));
    $reporter = new KiteReporter($config, $collector);

    // Add Laravel-ecosystem-specific actions
    $reporter->addAction(new GetLaravelVersionAction());
    $reporter->addAction(new GetStatamicVersionAction());
    $reporter->addAction(new GetLivewireVersionAction());
    $reporter->addAction(new GetFilamentVersionAction());

    $result = $reporter->report();

    if (! $result->success) {
        $this->error($result->message);
        return $this->option('quiet') ? self::SUCCESS : self::FAILURE;
    }
    return self::SUCCESS;
}
```

### 2.4 Updated `config/kite.php`

The `actions` key becomes optional (for overrides only). Default actions come from `KiteReporter::defaultActions()` (core) + the ones added in the command (Laravel-specific). If the config key is provided, it overrides the defaults entirely.

### 2.5 `LaravelProjectInfoCollector`

Extracts logic from current `GetProjectInformationAction` into a class implementing `ProjectInfoCollectorInterface::collect()`. Returns hostname, debug mode, environment, Laravel version, maintenance mode, PHP version, URL, and Composer packages.

---

## Implementation Steps

1. **Create `concept7/kite` package** — `composer.json`, `Support/Helpers`, contracts, `KiteConfig`, `ReportResult`, `Pipeline`, `KiteHttpClient`, core actions (PHP, MySQL, Tailwind, Vite + generic base classes), `KiteReporter`
2. **Refactor `concept7/laravel-kite`** — update `composer.json`, delete migrated actions, refactor Statamic/Livewire/Filament to extend core base class, create `LaravelProjectInfoCollector`, refactor command, update config
3. **Test both packages** — unit tests for Pipeline, Helpers, each action, KiteReporter; integration test for the Laravel command

## Verification

- Run `composer validate` on both packages
- Run the existing test suite in `laravel-kite` after refactoring
- Manually run `php artisan kite:report` in a Laravel project to verify the full flow
- Verify that the core package has zero `illuminate/*` imports via `grep -r "illuminate" src/` in the kite repo
