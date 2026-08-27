<?php

namespace Concept7\LaravelKite;

use Concept7\LaravelKite\Commands\LaravelKiteCheckAdvisoriesCommand;
use Concept7\LaravelKite\Commands\LaravelKiteReportCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class LaravelKiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kite.php', 'kite');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kite.php' => $this->app->configPath('kite.php'),
            ], ['kite', 'kite-config']);

            $this->commands([
                LaravelKiteReportCommand::class,
                LaravelKiteCheckAdvisoriesCommand::class,
            ]);
        }

        $schedule = $this->app->make(Schedule::class);

        // kite:report is triggered by the deploy pipeline (artisan:kite:report task),
        // which is the actual moment package/version data changes. A daily cron on
        // top of that is redundant and can race kite:check-advisories below, since
        // both independently scan and submit advisories for the same project.
        $schedule->command(LaravelKiteCheckAdvisoriesCommand::class)->hourly();
    }
}
