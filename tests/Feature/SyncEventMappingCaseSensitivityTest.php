<?php

namespace Tests\Feature;

use App\Models\CalendarConnection;
use App\Models\SyncEventMapping;
use App\Models\SyncRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies that source_event_id lookups and the unique index treat case as
 * significant. Microsoft Graph IDs are base64-encoded — `H` and `h` represent
 * different events. Case-insensitive collation merges them onto a single
 * mapping row and causes the blocker to flip-flop between their times.
 *
 * On SQLite (in-memory test DB) string comparison is case-sensitive by
 * default, which matches the behavior we enforce on MySQL via the
 * `2026_05_06_143600_fix_source_event_id_case_sensitive_collation` migration.
 */
class SyncEventMappingCaseSensitivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_mapping_is_case_sensitive(): void
    {
        [$rule, $target] = $this->makeRuleAndTarget();

        SyncEventMapping::create([
            'sync_rule_id' => $rule->id,
            'source_connection_id' => $rule->source_connection_id,
            'source_calendar_id' => 'primary',
            'source_event_id' => 'AAQG8ReHAAA=',
            'target_connection_id' => $target->id,
            'target_calendar_id' => 'cal-target',
            'target_event_id' => 'blocker-1',
        ]);

        $this->assertNotNull(
            SyncEventMapping::findMapping($rule->id, 'AAQG8ReHAAA=', $target->id, 'cal-target'),
            'Exact-case lookup should hit'
        );

        $this->assertNull(
            SyncEventMapping::findMapping($rule->id, 'AAQG8RehAAA=', $target->id, 'cal-target'),
            'Case-different lookup must not match — otherwise two distinct events collapse onto one mapping'
        );
    }

    public function test_two_mappings_with_case_different_ids_can_coexist(): void
    {
        [$rule, $target] = $this->makeRuleAndTarget();

        SyncEventMapping::create([
            'sync_rule_id' => $rule->id,
            'source_connection_id' => $rule->source_connection_id,
            'source_calendar_id' => 'primary',
            'source_event_id' => 'AAQG8ReHAAA=',
            'target_connection_id' => $target->id,
            'target_calendar_id' => 'cal-target',
            'target_event_id' => 'blocker-h-upper',
        ]);

        SyncEventMapping::create([
            'sync_rule_id' => $rule->id,
            'source_connection_id' => $rule->source_connection_id,
            'source_calendar_id' => 'primary',
            'source_event_id' => 'AAQG8RehAAA=',
            'target_connection_id' => $target->id,
            'target_calendar_id' => 'cal-target',
            'target_event_id' => 'blocker-h-lower',
        ]);

        $this->assertSame(2, SyncEventMapping::where('sync_rule_id', $rule->id)->count());
    }

    private function makeRuleAndTarget(): array
    {
        $user = User::factory()->create();
        $source = CalendarConnection::factory()->create([
            'user_id' => $user->id,
            'provider' => 'microsoft',
            'status' => 'active',
        ]);
        $target = CalendarConnection::factory()->create([
            'user_id' => $user->id,
            'provider' => 'microsoft',
            'status' => 'active',
        ]);
        $rule = SyncRule::factory()->create([
            'user_id' => $user->id,
            'source_connection_id' => $source->id,
        ]);

        return [$rule, $target];
    }
}
