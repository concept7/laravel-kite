<?php

return [
    'token' => env('KITE_TOKEN'),

    // Optional: override the Kite API base URL (for development)
    'uri' => env('KITE_URI'),

    'actions' => [],

    // Skip a scheduled advisory scan if kite:report ran more recently than this,
    // so the two commands never submit two independent advisory scans for the
    // same project moments apart.
    'advisories_min_minutes_after_report' => env('KITE_ADVISORIES_MIN_MINUTES_AFTER_REPORT', 15),
];
