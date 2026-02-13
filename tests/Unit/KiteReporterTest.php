<?php

use Concept7\Kite\Contracts\ActionInterface;
use Concept7\Kite\Contracts\ProjectInfoCollectorInterface;
use Concept7\Kite\Http\KiteHttpClient;
use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\Kite\ReportResult;
use Concept7\Kite\Support\Collection;

test('report fails with invalid config', function () {
    $config = new KiteConfig(uri: '', projectId: '', projectKey: '');
    $reporter = Kite::make($config);

    $result = $reporter->report();

    expect($result->success)->toBeFalse();
    expect($result->message)->toBe('Project credentials are missing!');
});

test('report sends meta data through pipeline', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com',
        projectId: '1',
        projectKey: 'secret',
    );

    $httpClient = Mockery::mock(KiteHttpClient::class);
    $httpClient->shouldReceive('send')
        ->once()
        ->withArgs(function (KiteConfig $cfg, array $payload) {
            // Should have meta with at least php_version from default actions
            $keys = array_column($payload['meta'], 'key');

            return in_array('php_version', $keys);
        })
        ->andReturn(ReportResult::success());

    $result = Kite::make($config)
        ->setHttpClient($httpClient)
        ->report();

    expect($result->success)->toBeTrue();
});

test('report includes project info when collector is provided', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com',
        projectId: '1',
        projectKey: 'secret',
    );

    $collector = Mockery::mock(ProjectInfoCollectorInterface::class);
    $collector->shouldReceive('collect')
        ->once()
        ->andReturn(['hostname' => 'test-server']);

    $httpClient = Mockery::mock(KiteHttpClient::class);
    $httpClient->shouldReceive('send')
        ->once()
        ->withArgs(function (KiteConfig $cfg, array $payload) {
            return isset($payload['project_info'])
                && $payload['project_info']['hostname'] === 'test-server';
        })
        ->andReturn(ReportResult::success());

    $result = Kite::make($config)
        ->setHttpClient($httpClient)
        ->projectInfoCollector($collector)
        ->report();

    expect($result->success)->toBeTrue();
});

test('report filters empty values from meta', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com',
        projectId: '1',
        projectKey: 'secret',
    );

    $emptyAction = new class implements ActionInterface
    {
        public function handle(Collection $data, Closure $next): Collection
        {
            $data->push(['key' => 'empty_value', 'value' => null]);

            return $next($data);
        }
    };

    $httpClient = Mockery::mock(KiteHttpClient::class);
    $httpClient->shouldReceive('send')
        ->once()
        ->withArgs(function (KiteConfig $cfg, array $payload) {
            $keys = array_column($payload['meta'], 'key');

            return ! in_array('empty_value', $keys);
        })
        ->andReturn(ReportResult::success());

    $result = Kite::make($config)
        ->setHttpClient($httpClient)
        ->setActions([$emptyAction])
        ->report();

    expect($result->success)->toBeTrue();
});

test('addAction appends to existing actions', function () {
    $config = new KiteConfig(
        uri: 'https://kite.example.com',
        projectId: '1',
        projectKey: 'secret',
    );

    $customAction = new class implements ActionInterface
    {
        public function handle(Collection $data, Closure $next): Collection
        {
            $data->push(['key' => 'custom', 'value' => 'yes']);

            return $next($data);
        }
    };

    $httpClient = Mockery::mock(KiteHttpClient::class);
    $httpClient->shouldReceive('send')
        ->once()
        ->withArgs(function (KiteConfig $cfg, array $payload) {
            $keys = array_column($payload['meta'], 'key');

            return in_array('php_version', $keys) && in_array('custom', $keys);
        })
        ->andReturn(ReportResult::success());

    $result = Kite::make($config)
        ->setHttpClient($httpClient)
        ->addAction($customAction)
        ->report();

    expect($result->success)->toBeTrue();
});
