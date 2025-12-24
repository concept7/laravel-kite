<?php

namespace Concept7\MonitorClient\Commands;

use Concept7\MonitorClient\Actions\GetProjectInformationAction;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Http;

class MonitorReportCommand extends Command
{
    public $signature = 'monitor:report';

    public $description = 'Report project data to monitor.';

    public function handle(): int
    {
        if (blank(config('monitor-client.project_key')) || blank(config('monitor-client.project_id'))) {
            $this->error('Project credentials are missing!');

            return self::FAILURE;
        }

        $meta = app(Pipeline::class)
            ->send(collect([]))
            ->through(config('monitor-client.actions', []))
            ->thenReturn();

        $projectInfo = app(GetProjectInformationAction::class)->handle();

        $response = Http::withOptions([
            'verify' => false,
        ])
            ->accept('application/json')
            ->withToken(config('monitor-client.project_key'))
            ->post(config('monitor-client.monitor_uri').'/api/project/'.config('monitor-client.project_id'), [
                'meta' => $meta->toArray(),
                'project_info' => $projectInfo,
            ]);

        if (! $response->ok()) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
