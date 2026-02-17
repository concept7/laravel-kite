<?php

use Concept7\Kite\Contracts\ActionInterface;
use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Illuminate\Support\Collection;

test('report throws with invalid config', function () {
    $config = new KiteConfig(uri: '', projectId: '', projectKey: '');
    $reporter = Kite::make($config);

    $reporter->report();
})->throws(Exception::class, 'Project credentials are missing!');
