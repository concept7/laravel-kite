<?php

namespace Concept7\LaravelKite\Jobs;

use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\LaravelKite\ProjectInfo\LaravelProjectInfoCollector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReportJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $config = new KiteConfig(
            token: config('kite.token'),
            uri: config('kite.uri'),
            monitoredPackages: config('kite.monitored_packages', []),
        );

        if (! $config->isValid()) {
            return;
        }

        $actions = array_map(fn (string $action): object => new $action, config('kite.actions', []));

        Kite::make($config)
            ->projectInfoCollector(new LaravelProjectInfoCollector)
            ->addActions($actions)
            ->report();
    }
}
