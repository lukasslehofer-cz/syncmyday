<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Synchronization Time Range
    |--------------------------------------------------------------------------
    |
    | Define how far back and forward to sync calendar events.
    | This prevents syncing very old or far-future events (like recurring
    | birthdays decades ahead) which can cause performance issues and Y2038 bugs.
    |
    */

    'time_range' => [
        // How many days in the past to sync (catches retroactive changes/cancellations)
        'past_days' => env('SYNC_PAST_DAYS', 7),

        // How many months in the future to sync
        'future_months' => env('SYNC_FUTURE_MONTHS', 6),

        // Hard limit year to prevent Y2038 MySQL TIMESTAMP issues
        'max_year' => 2037,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sync Behavior
    |--------------------------------------------------------------------------
    */

    'behavior' => [
        // Whether to sync all-day events
        'sync_all_day_events' => env('SYNC_ALL_DAY_EVENTS', true),

        // Minimum event duration in minutes to sync (0 = all events)
        'min_event_duration_minutes' => env('SYNC_MIN_DURATION', 0),

        // Maximum number of events to process per sync run
        'max_events_per_sync' => env('SYNC_MAX_EVENTS', 1000),
    ],
];

