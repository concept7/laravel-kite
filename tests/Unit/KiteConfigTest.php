<?php

use Concept7\Kite\KiteConfig;

test('isValid returns true when all required fields are set', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com',
        projectId: '123',
        projectKey: 'secret',
    );

    expect($config->isValid())->toBeTrue();
});

test('isValid returns false when uri is empty', function () {
    $config = new KiteConfig(uri: '', projectId: '123', projectKey: 'secret');
    expect($config->isValid())->toBeFalse();
});

test('isValid returns false when projectId is empty', function () {
    $config = new KiteConfig(uri: 'https://kite.example.com', projectId: '', projectKey: 'secret');
    expect($config->isValid())->toBeFalse();
});

test('isValid returns false when projectKey is empty', function () {
    $config = new KiteConfig(uri: 'https://kite.example.com', projectId: '123', projectKey: '');
    expect($config->isValid())->toBeFalse();
});

test('apiUrl builds the correct URL', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com',
        projectId: '42',
        projectKey: 'secret',
    );

    expect($config->apiUrl())->toBe('https://kite.example.com/api/project/42');
});

test('apiUrl trims trailing slash from uri', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com/',
        projectId: '42',
        projectKey: 'secret',
    );

    expect($config->apiUrl())->toBe('https://kite.example.com/api/project/42');
});
