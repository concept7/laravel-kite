<?php

namespace Concept7\LaravelKite\Actions;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GetMysqlVersionAction
{
    public function handle(Collection $data, Closure $next)
    {
        $process = Process::run('mysql --version');

        $output = $process->output();

        if (blank($output)) {
            return $next($data);
        }

        $database = 'mysql_';
        if (Str::of($output)->lower()->contains('mariadb')) {
            $database = 'mariadb_';
        }

        preg_match("@[0-9]+\.[0-9]+\.[0-9]+@", $output, $version);

        $database = $database.$version[0];

        $data->push([
            'key' => 'database_version',
            'value' => $database,
        ]);

        return $next($data);
    }
}
