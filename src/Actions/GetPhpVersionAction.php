<?php

namespace Concept7\MonitorClient\Actions;

use Closure;
use Illuminate\Support\Collection;

class GetPhpVersionAction
{
    public function handle(Collection $data, Closure $next)
    {
        $data->push([
            'key' => 'php_version',
            'value' => phpversion(),
        ]);

        return $next($data);
    }
}
