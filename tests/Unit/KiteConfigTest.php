<?php

use Concept7\Kite\KiteConfig;

test('isValid returns true when token is set', function () {
    $config = new KiteConfig(token: 'secret');

    expect($config->isValid())->toBeTrue();
});

test('isValid returns true with optional uri override', function () {
    $config = new KiteConfig(token: 'secret', uri: 'https://kite.local');

    expect($config->isValid())->toBeTrue();
});

test('isValid returns false when token is empty', function () {
    $config = new KiteConfig(token: '');
    expect($config->isValid())->toBeFalse();
});

test('isValid returns false when token is null', function () {
    $config = new KiteConfig;
    expect($config->isValid())->toBeFalse();
});
