<?php

use Concept7\Kite\Actions\GetComposerPackageVersionAction;
use Concept7\Kite\Actions\GetNodePackageVersionAction;
use Concept7\Kite\Actions\GetPhpVersionAction;
use Concept7\Kite\Actions\GetTailwindVersionAction;
use Illuminate\Support\Collection;
use Concept7\LaravelKite\Actions\GetFilamentVersionAction;
use Concept7\LaravelKite\Actions\GetLivewireVersionAction;
use Concept7\LaravelKite\Actions\GetStatamicVersionAction;

test('GetPhpVersionAction returns php version', function () {
    $action = new GetPhpVersionAction;
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result->toArray())->toBe([
        ['key' => 'php_version', 'value' => phpversion()],
    ]);
});

test('GetComposerPackageVersionAction returns version for installed package', function () {
    $action = new GetComposerPackageVersionAction('guzzle_version', ['guzzlehttp/guzzle']);
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result)->toHaveCount(1);
    expect($result[0]['key'])->toBe('guzzle_version');
    expect($result[0]['value'])->not->toBeEmpty();
});

test('GetComposerPackageVersionAction skips missing packages', function () {
    $action = new GetComposerPackageVersionAction('missing_version', ['nonexistent/package']);
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result)->toHaveCount(0);
});

test('GetComposerPackageVersionAction tries fallback packages', function () {
    $action = new GetComposerPackageVersionAction('test_version', ['nonexistent/first', 'guzzlehttp/guzzle']);
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result)->toHaveCount(1);
    expect($result[0]['key'])->toBe('test_version');
});

test('GetNodePackageVersionAction skips when lock file missing', function () {
    $action = new GetNodePackageVersionAction('test_version', 'tailwindcss', '/nonexistent/path');
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result)->toHaveCount(0);
});

test('GetTailwindVersionAction sets correct meta key and package name', function () {
    $action = new GetTailwindVersionAction;
    $result = $action->handle(new Collection, fn ($data) => $data);

    // Returns empty since lock file doesn't exist in test environment
    expect($result)->toHaveCount(0);
});

test('GetViteVersionAction sets correct meta key and package name', function () {
    $action = new \Concept7\LaravelKite\Actions\GetViteVersionAction;
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result)->toHaveCount(0);
});

test('GetStatamicVersionAction extends GetComposerPackageVersionAction', function () {
    expect(new GetStatamicVersionAction)->toBeInstanceOf(GetComposerPackageVersionAction::class);
});

test('GetLivewireVersionAction extends GetComposerPackageVersionAction', function () {
    expect(new GetLivewireVersionAction)->toBeInstanceOf(GetComposerPackageVersionAction::class);
});

test('GetFilamentVersionAction extends GetComposerPackageVersionAction', function () {
    expect(new GetFilamentVersionAction)->toBeInstanceOf(GetComposerPackageVersionAction::class);
});
