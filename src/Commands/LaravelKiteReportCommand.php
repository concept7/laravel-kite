<?php

namespace Concept7\LaravelKite\Commands;

use Concept7\Kite\Kite;
use Concept7\Kite\KiteConfig;
use Concept7\LaravelKite\ProjectInfo\LaravelProjectInfoCollector;
use Illuminate\Console\Command;

class LaravelKiteReportCommand extends Command
{
    public $signature = 'kite:report';

    public $description = 'Report project data to Kite.';

    public function handle(): int
    {
        $config = new KiteConfig(
            uri: config('kite.uri'),
            projectId: config('kite.project_id'),
            projectKey: config('kite.project_key'),
        );

        if (! $config->isValid()) {
            $this->error('Project credentials are missing!');

            if ($this->option('quiet')) {
                return self::SUCCESS;
            }

            return self::FAILURE;
        }

        $actions = array_map(fn ($action) => new $action, config('kite.actions', []));

        $collector = new LaravelProjectInfoCollector;
        $projectInfo = $collector->collect();

        try {
            Kite::make($config)
                ->projectInfoCollector($collector)
                ->addActions($actions)
                ->report();

            $this->line(sprintf(
                'Reporting <info>%s</info> · <info>%d</info> packages · <info>%d</info> actions',
                $projectInfo['environment'],
                count($projectInfo['packages']),
                count($actions),
            ));
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            if ($this->option('quiet')) {
                return self::SUCCESS;
            }

            return self::FAILURE;
        }

        $this->info('Report sent successfully.');

        return self::SUCCESS;
    }
}
