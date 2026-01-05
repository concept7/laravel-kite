<?php

namespace Concept7\LaravelKite;

use Concept7\LaravelKite\Commands\ReportCommand;
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
            ->hasCommand(ReportCommand::class);
    }

    public function packageBooted()
    {
        $schedule = $this->app->make(Schedule::class);

        $schedule->command(ReportCommand::class)->weekly();
    }
}
