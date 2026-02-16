<?php

use Concept7\Kite\Actions\GetPhpVersionAction;
use Concept7\Kite\Contracts\ActionInterface;
use Illuminate\Support\Collection;
use Concept7\Kite\Support\Pipeline;

test('pipeline passes data through actions in order', function () {
    $actionA = new class implements ActionInterface
    {
        public function handle(Collection $data, Closure $next): Collection
        {
            $data->push(['key' => 'a', 'value' => '1']);

            return $next($data);
        }
    };

    $actionB = new class implements ActionInterface
    {
        public function handle(Collection $data, Closure $next): Collection
        {
            $data->push(['key' => 'b', 'value' => '2']);

            return $next($data);
        }
    };

    $result = (new Pipeline)
        ->send(new Collection)
        ->through([$actionA, $actionB])
        ->thenReturn();

    expect($result->toArray())->toBe([
        ['key' => 'a', 'value' => '1'],
        ['key' => 'b', 'value' => '2'],
    ]);
});

test('pipeline returns initial data when no actions', function () {
    $result = (new Pipeline)
        ->send(new Collection)
        ->through([])
        ->thenReturn();

    expect($result->toArray())->toBe([]);
});

test('pipeline resolves action instances', function () {
    $result = (new Pipeline)
        ->send(new Collection)
        ->through([new GetPhpVersionAction])
        ->thenReturn();

    expect($result)->toHaveCount(1);
    expect($result[0]['key'])->toBe('php_version');
    expect($result[0]['value'])->toBe(phpversion());
});
