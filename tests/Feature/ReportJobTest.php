<?php

use Concept7\LaravelKite\Jobs\ReportJob;
use Illuminate\Support\Facades\Cache;

test('does not record a run when credentials are missing', function () {
    config()->set('kite.token', '');
    Cache::forget(ReportJob::LAST_RAN_AT_CACHE_KEY);

    (new ReportJob)->handle();

    expect(Cache::get(ReportJob::LAST_RAN_AT_CACHE_KEY))->toBeNull();
});
