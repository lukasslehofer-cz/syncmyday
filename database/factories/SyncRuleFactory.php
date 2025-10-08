<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\CalendarConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

class SyncRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source_connection_id' => CalendarConnection::factory(),
            'source_calendar_id' => 'primary',
            'direction' => 'one_way',
            'blocker_title' => 'Busy — Sync',
            'filters' => [
                'busy_only' => true,
                'ignore_all_day' => false,
            ],
            'is_active' => true,
        ];
    }
}

