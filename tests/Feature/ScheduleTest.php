<?php

use Illuminate\Console\Scheduling\Schedule;

test('kite:check-advisories is scheduled hourly but kite:report is not scheduled at all', function () {
    $events = app(Schedule::class)->events();

    $commands = collect($events)->map(fn ($event) => $event->command);

    expect($commands->contains(fn (string $command): bool => str_contains($command, 'kite:check-advisories')))->toBeTrue()
        ->and($commands->contains(fn (string $command): bool => str_contains($command, 'kite:report')))->toBeFalse();
});
