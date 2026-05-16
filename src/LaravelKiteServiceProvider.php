<?php

namespace Concept7\LaravelKite;

use Concept7\LaravelKite\Commands\LaravelKiteCheckAdvisoriesCommand;
use Concept7\LaravelKite\Commands\LaravelKiteReportCommand;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelKiteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-kite')
            ->hasConfigFile()
            ->hasCommands([
                LaravelKiteReportCommand::class,
                LaravelKiteCheckAdvisoriesCommand::class,
            ]);
    }

    public function packageBooted()
    {
        $schedule = $this->app->make(Schedule::class);

        $schedule->command(LaravelKiteReportCommand::class)->daily();
        $schedule->command(LaravelKiteCheckAdvisoriesCommand::class)->hourly();
    }
}
