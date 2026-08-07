# Laravel Kite

Laravel integration for [Kite](https://gitlab.concept7.nl/workflow/kite) monitoring. Reports project metadata (PHP, Node, database versions, installed Composer/npm packages) to the Kite API, and scans those packages for security advisories.

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

    // Skip a scheduled advisory scan if kite:report ran more recently than this,
    // so the two commands never submit two independent advisory scans for the
    // same project moments apart.
    'advisories_min_minutes_after_report' => env('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT', 15),
];
```

| Key | Environment variable | Description |
|---|---|---|
| `token` | `KITE_TOKEN` | API token (generated from the dashboard) |
| `uri` | `KITE_URI` | Optional. Override the Kite API base URL |
| `actions` | | Extra actions to run alongside the defaults |
| `advisories_min_minutes_after_report` | `KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT` | Minutes to skip a scheduled advisory scan after `kite:report` last ran |

## Usage

Two Artisan commands are available:

```bash
php artisan kite:report              # Report project metadata (and run an inline advisory scan)
php artisan kite:check-advisories    # Run a standalone advisory scan
```

`kite:report` is **not** scheduled automatically — it's meant to be triggered by your deploy pipeline (the moment package/version data actually changes), or run manually. `kite:check-advisories` **is** scheduled automatically, hourly, as a safety net between deploys; it skips itself if `kite:report` already ran within `advisories_min_minutes_after_report` minutes, so the two never submit two independent scans moments apart.

### When to run `kite:report`

Run it whenever your project's dependencies or environment could have changed — that's when the data Kite shows would otherwise go stale. In practice, that's every deploy.

**Post-deploy (recommended)** — add it as a step in your deploy pipeline, after `composer install`/`npm install` have run, right alongside your other post-deploy Artisan calls (`migrate`, `config:cache`, etc.):

```bash
php artisan kite:report
```

**No deploy pipeline?** Schedule it yourself in `routes/console.php` (or `app/Console/Kernel.php` on older Laravel versions) instead:

```php
use Concept7\LaravelKite\Commands\LaravelKiteReportCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(LaravelKiteReportCommand::class)->daily();
```

Don't add both — a deploy hook and a daily schedule reporting the same static data is redundant. Pick whichever matches how often your project's dependencies actually change.

### What gets reported

**Meta** — Version information collected by actions. The core SDK (`concept7/kite-php-sdk`) provides default actions for PHP, Node, and MySQL/MariaDB versions. The `actions` array in the config adds extra actions on top.

**Project info** — Collected automatically by `LaravelProjectInfoCollector`:

| Field | Description |
|---|---|
| `hostname` | Server hostname |
| `environment` | App environment (`production`, `staging`, etc.) |
| `is_debug_mode_on` | Whether debug mode is enabled |
| `is_maintenance_mode_on` | Whether maintenance mode is active |
| `url` | Application URL |
| `packages` | Installed Composer and npm packages |

**Advisories** — `kite:report` also scans the collected packages for security advisories and includes them in the same payload (failures here don't block the report). `kite:check-advisories` runs that same scan on its own schedule.

## Actions

The `actions` array in `config/kite.php` adds extra actions on top of the SDK's PHP/Node/MySQL defaults.

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

## Testing

```bash
composer test         # Full gate: analyse + lint:check + test:types + test:unit
composer test:unit     # Run tests (Pest v4)
composer lint          # Format code (Laravel Pint)
composer analyse       # Static analysis (PHPStan/Larastan)
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
