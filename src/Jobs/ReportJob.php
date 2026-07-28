<?php

namespace Concept7\LaravelKite\Jobs;

use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\LaravelKite\ProjectInfo\LaravelProjectInfoCollector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class ReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * When the last report actually ran, so CheckAdvisoriesJob can skip a
     * scan that would otherwise race the advisories this report just sent.
     */
    public const LAST_RAN_AT_CACHE_KEY = 'kite:report:last-ran-at';

    public function handle(): void
    {
        $config = new KiteConfig(
            token: config('kite.token'),
            uri: config('kite.uri'),
        );

        if (! $config->isValid()) {
            return;
        }

        $actions = array_map(
            fn (string $action): object => new $action,
            config('kite.actions', []),
        );

        Kite::make($config)
            ->projectInfoCollector(new LaravelProjectInfoCollector)
            ->addActions($actions)
            ->report();

        Cache::put(self::LAST_RAN_AT_CACHE_KEY, now(), now()->addHours(2));
    }
}
