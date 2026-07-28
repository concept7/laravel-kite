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

        // kite:report is triggered by the deploy pipeline (artisan:kite:report task),
        // which is the actual moment package/version data changes. A daily cron on
        // top of that is redundant and can race kite:check-advisories below, since
        // both independently scan and submit advisories for the same project.
        $schedule->command(LaravelKiteCheckAdvisoriesCommand::class)->hourly();
    }
}
