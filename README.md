# Laravel Kite

Laravel integration for [Kite](https://gitlab.concept7.nl/workflow/kite) monitoring. Automatically reports project metadata (PHP, Laravel, database, frontend tooling, installed packages) to the Kite API on a daily schedule.

## Installation

Add the Concept7 Composer repository to your project:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packagist.concept7.nl"
        }
    ]
}
```

Install the package:

```bash
composer require concept7/laravel-kite
```

Publish the config file:

```bash
php artisan vendor:publish --tag="laravel-kite-config"
```

Add the following environment variables to your `.env` file:

```env
KITE_URI=https://kite.example.com
KITE_PROJECT_ID=your-project-id
KITE_PROJECT_KEY=your-project-key
```

## Configuration

The published config file (`config/kite.php`):

```php
return [
    'uri' => env('KITE_URI'),

    'project_id' => env('KITE_PROJECT_ID'),
    'project_key' => env('KITE_PROJECT_KEY'),

    'php_path' => env('KITE_PHP_PATH', 'php'),

    'actions' => [
        \Concept7\LaravelKite\Actions\GetLaravelVersionAction::class,
        \Concept7\LaravelKite\Actions\GetStatamicVersionAction::class,
        \Concept7\LaravelKite\Actions\GetLivewireVersionAction::class,
        \Concept7\LaravelKite\Actions\GetFilamentVersionAction::class,
    ],
];
```

| Key | Environment variable | Description |
|---|---|---|
| `uri` | `KITE_URI` | Base URL of the Kite API |
| `project_id` | `KITE_PROJECT_ID` | Project ID in Kite |
| `project_key` | `KITE_PROJECT_KEY` | API key for authentication |
| `php_path` | `KITE_PHP_PATH` | Optional. Path to PHP binary (default: `php`) |
| `actions` | | Extra actions to run alongside the defaults |

## Usage

The `kite:report` command runs automatically on a daily schedule. To run it manually:

```bash
php artisan kite:report
```

### What gets reported

The report includes two parts:

**Meta** — Version information collected by actions. The core `concept7/kite` package provides default actions for PHP, MySQL/MariaDB, and Tailwind CSS versions. The `actions` array in the config adds Laravel-specific actions on top of those.

**Project info** — Collected automatically by `LaravelProjectInfoCollector`:

| Field | Description |
|---|---|
| `hostname` | Server hostname |
| `environment` | App environment (`production`, `staging`, etc.) |
| `is_debug_mode_on` | Whether debug mode is enabled |
| `is_maintenance_mode_on` | Whether maintenance mode is active |
| `laravel_version` | Laravel framework version |
| `php_version` | PHP version |
| `url` | Application URL |
| `packages` | List of direct Composer dependencies |

## Actions

### Included actions

| Action | Meta key | Description |
|---|---|---|
| `GetLaravelVersionAction` | `laravel_version` | Laravel framework version |
| `GetStatamicVersionAction` | `statamic_version` | Statamic CMS version |
| `GetLivewireVersionAction` | `livewire_version` | Livewire version |
| `GetFilamentVersionAction` | `filament_version` | Filament version |
| `GetViteVersionAction` | `vite_version` | Vite version (from `package-lock.json`) |

Actions for packages that aren't installed are automatically skipped.

### Adding a custom action

Add the action class to the `actions` array in `config/kite.php`:

```php
'actions' => [
    \Concept7\LaravelKite\Actions\GetLaravelVersionAction::class,
    \Concept7\LaravelKite\Actions\GetViteVersionAction::class,
    \App\Kite\GetCustomMetaAction::class,
],
```

A custom action implements `ActionInterface`:

```php
namespace App\Kite;

use Closure;
use Concept7\Kite\Contracts\ActionInterface;
use Illuminate\Support\Collection;

class GetCustomMetaAction implements ActionInterface
{
    public function handle(Collection $data, Closure $next): Collection
    {
        $data->push([
            'key' => 'custom_meta',
            'value' => 'your-value',
        ]);

        return $next($data);
    }
}
```

You can also use the built-in base classes for common patterns:

```php
use Concept7\Kite\Actions\GetComposerPackageVersionAction;

// Track a Composer package version
class GetInertiaVersionAction extends GetComposerPackageVersionAction
{
    public function __construct()
    {
        parent::__construct('inertia_version', ['inertiajs/inertia-laravel']);
    }
}
```

```php
use Concept7\Kite\Actions\GetNodePackageVersionAction;

// Track a Node package version from package-lock.json
class GetAlpineVersionAction extends GetNodePackageVersionAction
{
    public function __construct()
    {
        parent::__construct('alpine_version', 'alpinejs');
    }
}
```

### Removing actions

Remove any actions you don't need from the `actions` array. For example, if your project doesn't use Statamic or Filament:

```php
'actions' => [
    \Concept7\LaravelKite\Actions\GetLaravelVersionAction::class,
    \Concept7\LaravelKite\Actions\GetLivewireVersionAction::class,
],
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
