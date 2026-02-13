<?php

namespace Concept7\LaravelKite\Actions;

use Closure;
use Concept7\Kite\Contracts\ActionInterface;
use Concept7\Kite\Support\Collection;

class GetLaravelVersionAction implements ActionInterface
{
    public function handle(Collection $data, Closure $next): Collection
    {
        $data->push([
            'key' => 'laravel_version',
            'value' => app()->version(),
        ]);

        return $next($data);
    }
}
