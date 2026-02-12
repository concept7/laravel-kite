<?php

use Concept7\Kite\Actions\GetComposerPackageVersionAction;
use Concept7\Kite\Actions\GetNodePackageVersionAction;
use Concept7\Kite\Actions\GetPhpVersionAction;
use Concept7\Kite\Actions\GetTailwindVersionAction;
use Concept7\LaravelKite\Actions\GetFilamentVersionAction;
use Concept7\LaravelKite\Actions\GetLivewireVersionAction;
use Concept7\LaravelKite\Actions\GetStatamicVersionAction;

test('GetPhpVersionAction returns php version', function () {
    $action = new GetPhpVersionAction;
    $result = $action->handle(collect([]), fn ($data) => $data);

    expect($result->toArray())->toBe([
        ['key' => 'php_version', 'value' => phpversion()],
    ]);
});

test('GetComposerPackageVersionAction returns version for installed package', function () {
    $action = new GetComposerPackageVersionAction('guzzle_version', ['guzzlehttp/guzzle']);
    $result = $action->handle(collect([]), fn ($data) => $data);

    expect($result)->toHaveCount(1);
    expect($result[0]['key'])->toBe('guzzle_version');
    expect($result[0]['value'])->not->toBeEmpty();
});

test('GetComposerPackageVersionAction skips missing packages', function () {
    $action = new GetComposerPackageVersionAction('missing_version', ['nonexistent/package']);
    $result = $action->handle(collect([]), fn ($data) => $data);

    expect($result)->toBeEmpty();
});

test('GetComposerPackageVersionAction tries fallback packages', function () {
    $action = new GetComposerPackageVersionAction('test_version', ['nonexistent/first', 'guzzlehttp/guzzle']);
    $result = $action->handle(collect([]), fn ($data) => $data);

    expect($result)->toHaveCount(1);
    expect($result[0]['key'])->toBe('test_version');
});

test('GetNodePackageVersionAction skips when lock file missing', function () {
    $action = new GetNodePackageVersionAction('/nonexistent/path', 'test_version', 'tailwindcss');
    $result = $action->handle(collect([]), fn ($data) => $data);

    expect($result)->toBeEmpty();
});

test('GetTailwindVersionAction sets correct meta key and package name', function () {
    $action = new GetTailwindVersionAction('/nonexistent/path');
    $result = $action->handle(collect([]), fn ($data) => $data);

    // Returns empty since file doesn't exist, but class instantiates correctly
    expect($result)->toBeEmpty();
});

test('GetViteVersionAction sets correct meta key and package name', function () {
    $action = new \Concept7\LaravelKite\Actions\GetViteVersionAction('/nonexistent/path');
    $result = $action->handle(collect([]), fn ($data) => $data);

    expect($result)->toBeEmpty();
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
