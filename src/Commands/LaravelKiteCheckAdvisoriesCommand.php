<?php

namespace Concept7\LaravelKite\Commands;

use Concept7\LaravelKite\Jobs\CheckAdvisoriesJob;
use Illuminate\Console\Command;

class LaravelKiteCheckAdvisoriesCommand extends Command
{
    public $signature = 'kite:check-advisories';

    public $description = 'Dispatch an advisory scan for all packages to Kite.';

    public function handle(): int
    {
        dispatch(new CheckAdvisoriesJob);

        $this->info('Advisory check dispatched.');

        return self::SUCCESS;
    }
}
