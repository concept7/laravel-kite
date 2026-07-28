<?php

use Concept7\LaravelKite\Jobs\CheckAdvisoriesJob;
use Concept7\LaravelKite\Jobs\ReportJob;
use Illuminate\Support\Facades\Cache;

test('skips when kite:report ran within the configured interval', function () {
    config()->set('kite.advisories_min_minutes_after_report', 15);
    Cache::put(ReportJob::LAST_RAN_AT_CACHE_KEY, now()->subMinutes(5));

    expect((new CheckAdvisoriesJob)->reportRanTooRecently())->toBeTrue();
});

test('does not skip once the configured interval has passed', function () {
    config()->set('kite.advisories_min_minutes_after_report', 15);
    Cache::put(ReportJob::LAST_RAN_AT_CACHE_KEY, now()->subMinutes(20));

    expect((new CheckAdvisoriesJob)->reportRanTooRecently())->toBeFalse();
});

test('does not skip when kite:report has never run', function () {
    Cache::forget(ReportJob::LAST_RAN_AT_CACHE_KEY);

    expect((new CheckAdvisoriesJob)->reportRanTooRecently())->toBeFalse();
});

test('the interval is configurable', function () {
    config()->set('kite.advisories_min_minutes_after_report', 60);
    Cache::put(ReportJob::LAST_RAN_AT_CACHE_KEY, now()->subMinutes(30));

    expect((new CheckAdvisoriesJob)->reportRanTooRecently())->toBeTrue();
});
