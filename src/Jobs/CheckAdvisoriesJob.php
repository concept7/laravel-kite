<?php

namespace Concept7\LaravelKite\Jobs;

use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\LaravelKite\ProjectInfo\LaravelProjectInfoCollector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

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

        if ($this->reportRanTooRecently()) {
            return;
        }

        Kite::make($config)
            ->projectInfoCollector(new LaravelProjectInfoCollector)
            ->checkAdvisories();
    }

    /**
     * True when kite:report ran more recently than kite.advisories_min_minutes_after_report,
     * meaning it already submitted a fresh advisory scan for this project.
     */
    public function reportRanTooRecently(): bool
    {
        $lastReportRanAt = Cache::get(ReportJob::LAST_RAN_AT_CACHE_KEY);
        $minMinutesAfterReport = (int) config('kite.advisories_min_minutes_after_report', 15);

        return $lastReportRanAt?->gt(now()->subMinutes($minMinutesAfterReport)) ?? false;
    }
}
