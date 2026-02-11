<?php

use Concept7\Kite\ReportResult;

test('success creates a successful result', function () {
    $result = ReportResult::success(200);

    expect($result->success)->toBeTrue();
    expect($result->message)->toBe('');
    expect($result->statusCode)->toBe(200);
});

test('failure creates a failed result', function () {
    $result = ReportResult::failure('Something went wrong', 500);

    expect($result->success)->toBeFalse();
    expect($result->message)->toBe('Something went wrong');
    expect($result->statusCode)->toBe(500);
});

test('failure defaults statusCode to 0', function () {
    $result = ReportResult::failure('Network error');

    expect($result->statusCode)->toBe(0);
});
