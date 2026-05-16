<?php

test('command fails when credentials are missing', function () {
    config()->set('kite.token', '');

    $this->artisan('kite:report')
        ->expectsOutputToContain('Project credentials are missing!')
        ->assertExitCode(1);
});

test('command fails silently when credentials are missing and quiet', function () {
    config()->set('kite.token', '');

    $this->artisan('kite:report --quiet')
        ->assertExitCode(1);
});
