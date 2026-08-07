# Laravel Kite

Laravel integration for [Kite](https://gitlab.concept7.nl/workflow/kite) monitoring. Automatically reports project metadata (PHP, Laravel, database, frontend tooling, installed packages) to the Kite API on a daily schedule.

## Installation

Add the Concept7 Composer repository to your project:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packagist.concept7.dev"
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
php artisan vendor:publish --tag="kite-config"
```

Add the `KITE_TOKEN` to your `.env` file (generated from the [Kite Dashboard](https://kite-monitor.concept7.dev/)):

```env
KITE_TOKEN=your-kite-token
```

## Configuration

The published config file (`config/kite.php`):

```php
return [
    'token' => env('KITE_TOKEN'),

    // Optional: override the Kite API base URL (for development)
    'uri' => env('KITE_URI'),

    'actions' => [],
];
```

| Key | Environment variable | Description |
|---|---|---|
| `token` | `KITE_TOKEN` | API token (generated from the dashboard) |
| `uri` | `KITE_URI` | Optional. Override the Kite API base URL |
| `actions` | | Extra actions to run alongside the defaults |

## Usage

The `kite:report` command runs automatically on a daily schedule. To run it manually:

```bash
php artisan kite:report
```

### What gets reported

The report includes two parts:

**Meta** — Version information collected by actions. The core SDK provides default actions for PHP, MySQL/MariaDB, Tailwind CSS, and Kite SDK versions. The `actions` array in the config adds Laravel-specific actions on top.

**Project info** — Collected automatically by `LaravelProjectInfoCollector`:

| Field | Description |
|---|---|
| `hostname` | Server hostname |
| `environment` | App environment (`production`, `staging`, etc.) |
| `is_debug_mode_on` | Whether debug mode is enabled |
| `is_maintenance_mode_on` | Whether maintenance mode is active |
| `url` | Application URL |
| `packages` | Installed Composer and npm packages |

## Actions

The core SDK (`concept7/kite-php-sdk`) runs default actions for PHP version, MySQL/MariaDB version, Tailwind CSS, and Vite. The `actions` array in `config/kite.php` adds extra actions on top of those defaults.

### Adding a custom action

Add the action class to the `actions` array in `config/kite.php`:

```php
'actions' => [
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

class GetAlpineVersionAction extends GetNodePackageVersionAction
{
    public function __construct()
    {
        parent::__construct('alpine_version', 'alpinejs');
    }
}
```


## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
