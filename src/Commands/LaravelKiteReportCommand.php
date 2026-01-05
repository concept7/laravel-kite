<?php

namespace Concept7\LaravelKite\Commands;

use Concept7\LaravelKite\Actions\GetProjectInformationAction;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Http;

class LaravelKiteReportCommand extends Command
{
    public $signature = 'kite:report';

    public $description = 'Report project data to Kite.';

    public function handle(): int
    {
        if (blank(config('kite.project_key')) || blank(config('kite.project_id'))) {
            $this->error('Project credentials are missing!');

            return self::FAILURE;
        }

        $meta = app(Pipeline::class)
            ->send(collect([]))
            ->through(config('kite.actions', []))
            ->thenReturn();

        $projectInfo = app(GetProjectInformationAction::class)->handle();

        $response = Http::withOptions([
            'verify' => false,
        ])
            ->accept('application/json')
            ->withToken(config('kite.project_key'))
            ->post(config('kite.uri').'/api/project/'.config('kite.project_id'), [
                'meta' => $meta->toArray(),
                'project_info' => $projectInfo,
            ]);

        if (! $response->ok()) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
