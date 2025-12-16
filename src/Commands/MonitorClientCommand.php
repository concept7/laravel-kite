<?php

namespace Concept7\MonitorClient\Commands;

use Concept7\MonitorClient\Actions\GetProjectInformationAction;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Http;

class MonitorClientCommand extends Command
{
    public $signature = 'monitor-client';

    public $description = 'My command';

    public function handle(): int
    {
        $meta = app(Pipeline::class)
            ->send(collect([]))
            ->through(config('monitor-client.actions', []))
            ->thenReturn();

        $projectInfo = app(GetProjectInformationAction::class)->handle();

        $response = Http::withOptions([
            'verify' => false,
        ])
            ->accept('application/json')
            ->post(config('monitor-client.monitor_uri').'/api/project/'.config('monitor-client.project_id').'/'.config('monitor-client.project_key'), [
                'meta' => $meta->toArray(),
                'project_info' => $projectInfo,
            ]);

        if (! $response->ok()) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
