<?php

use Concept7\Kite\Actions\GetPhpVersionAction;
use Illuminate\Support\Collection;

test('GetPhpVersionAction returns php version', function () {
    $action = new GetPhpVersionAction;
    $result = $action->handle(new Collection, fn ($data) => $data);

    expect($result->toArray())->toBe([
        ['key' => 'php_version', 'value' => phpversion()],
    ]);
});
