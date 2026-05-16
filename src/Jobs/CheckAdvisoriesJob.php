<?php

namespace Concept7\LaravelKite\Jobs;

use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\LaravelKite\ProjectInfo\LaravelProjectInfoCollector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckAdvisoriesJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $config = new KiteConfig(
            token: config('kite.token'),
            uri: config('kite.uri'),
        );

        if (! $config->isValid()) {
            return;
        }

        Kite::make($config)
            ->projectInfoCollector(new LaravelProjectInfoCollector)
            ->checkAdvisories();
    }
}
