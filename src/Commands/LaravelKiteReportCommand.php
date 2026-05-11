<?php

namespace Concept7\LaravelKite\Commands;

use Concept7\LaravelKite\Jobs\ReportJob;
use Illuminate\Console\Command;

class LaravelKiteReportCommand extends Command
{
    public $signature = 'kite:report';

    public $description = 'Report project data to Kite.';

    public function handle(): int
    {
        dispatch(new ReportJob);

        $this->info('Report dispatched.');

        return self::SUCCESS;
    }
}
