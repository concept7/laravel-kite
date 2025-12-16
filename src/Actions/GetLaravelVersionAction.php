<?php

namespace Concept7\MonitorClient\Actions;

use Closure;
use Illuminate\Support\Collection;

class GetLaravelVersionAction
{
    public function handle(Collection $data, Closure $next)
    {
        $data->push([
            'key' => 'laravel_version',
            'value' => app()->version(),
        ]);

        return $next($data);
    }
}
