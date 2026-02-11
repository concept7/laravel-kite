<?php

use Concept7\Kite\KiteConfig;

test('isValid returns true when all required fields are set', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com',
        projectId: '123',
        projectKey: 'secret',
        projectRoot: '/var/www',
    );

    expect($config->isValid())->toBeTrue();
});

test('isValid returns false when uri is empty', function () {
    $config = new KiteConfig(uri: '', projectId: '123', projectKey: 'secret', projectRoot: '/var/www');
    expect($config->isValid())->toBeFalse();
});

test('isValid returns false when projectId is empty', function () {
    $config = new KiteConfig(uri: 'https://kite.example.com', projectId: '', projectKey: 'secret', projectRoot: '/var/www');
    expect($config->isValid())->toBeFalse();
});

test('isValid returns false when projectKey is empty', function () {
    $config = new KiteConfig(uri: 'https://kite.example.com', projectId: '123', projectKey: '', projectRoot: '/var/www');
    expect($config->isValid())->toBeFalse();
});

test('apiUrl builds the correct URL', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com',
        projectId: '42',
        projectKey: 'secret',
        projectRoot: '/var/www',
    );

    expect($config->apiUrl())->toBe('https://kite.example.com/api/project/42');
});

test('apiUrl trims trailing slash from uri', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com/',
        projectId: '42',
        projectKey: 'secret',
        projectRoot: '/var/www',
    );

    expect($config->apiUrl())->toBe('https://kite.example.com/api/project/42');
});

test('phpPath defaults to php', function () {
    $config = new KiteConfig(uri: 'https://x.com', projectId: '1', projectKey: 'k', projectRoot: '/');
    expect($config->phpPath)->toBe('php');
});
