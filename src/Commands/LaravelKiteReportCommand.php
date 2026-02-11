<?php

namespace Concept7\LaravelKite\Commands;

use Concept7\Kite\KiteConfig;
use Concept7\Kite\KiteReporter;
use Concept7\LaravelKite\ProjectInfo\LaravelProjectInfoCollector;
use Illuminate\Console\Command;

class LaravelKiteReportCommand extends Command
{
    public $signature = 'kite:report';

    public $description = 'Report project data to Kite.';

    public function handle(): int
    {
        $config = new KiteConfig(
            uri: config('kite.uri', ''),
            projectId: config('kite.project_id', ''),
            projectKey: config('kite.project_key', ''),
            projectRoot: base_path(),
            phpPath: config('kite.php_path', 'php'),
        );

        if (! $config->isValid()) {
            $this->error('Project credentials are missing!');

            if ($this->option('quiet')) {
                return self::SUCCESS;
            }

            return self::FAILURE;
        }

        $collector = new LaravelProjectInfoCollector(config('kite.php_path', 'php'));
        $reporter = new KiteReporter($config, $collector);
        $actions = array_map(fn ($action) => new $action, config('kite.actions', []));
        $reporter->addActions($actions);

        $result = $reporter->report();

        if (! $result->success) {
            $this->error($result->message);

            if ($this->option('quiet')) {
                return self::SUCCESS;
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
