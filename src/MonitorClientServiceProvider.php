<?php

namespace Concept7\MonitorClient;

use Concept7\MonitorClient\Commands\MonitorClientCommand;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MonitorClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('monitor-client')
            ->hasConfigFile()
            ->hasCommand(MonitorClientCommand::class);
    }

    public function packageBooted()
    {
        $schedule = $this->app->make(Schedule::class);

        $schedule->command(MonitorClientCommand::class)->weekly();
    }
}
