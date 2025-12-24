# This is my package monitor-client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/concept7/monitor-client.svg?style=flat-square)](https://packagist.org/packages/concept7/monitor-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/concept7/monitor-client/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/concept7/monitor-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/concept7/monitor-client/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/concept7/monitor-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/concept7/monitor-client.svg?style=flat-square)](https://packagist.org/packages/concept7/monitor-client)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/monitor-client.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/monitor-client)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

Add this Composer repository to your project's composer.json file.

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

You can install the package via composer:

```bash
composer require concept7/monitor-client
```

Optionally you can publish the config file with:

```bash
php artisan vendor:publish --tag="monitor-client-config"
```

This is the contents of the published config file:

```php
return [
    'monitor_uri' => env('MONITOR_URI'),

    'project_id' => env('MONITOR_CLIENT_PROJECT_ID'),
    'project_key' => env('MONITOR_CLIENT_PROJECT_KEY'),

    'actions' => [
        \Concept7\MonitorClient\Actions\GetPhpVersionAction::class,
        \Concept7\MonitorClient\Actions\GetLaravelVersionAction::class,
        \Concept7\MonitorClient\Actions\GetStatamicVersionAction::class,
        \Concept7\MonitorClient\Actions\GetLivewireVersionAction::class,
        \Concept7\MonitorClient\Actions\GetFilamentVersionAction::class,
        \Concept7\MonitorClient\Actions\GetTailwindVersion::class,
        \Concept7\MonitorClient\Actions\GetViteVersionAction::class,
    ],
];
```

## Usage

```bash
php artisan monitor:report
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Martijn Wagena](https://github.com/mwagena)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
