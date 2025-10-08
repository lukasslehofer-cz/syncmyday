<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CalendarConnection;
use App\Models\SyncRule;
use App\Models\SyncRuleTarget;
use App\Services\Sync\SyncEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test basic sync workflow
     * This is a simplified test that verifies the core components are wired correctly
     */
    public function test_can_create_sync_rule()
    {
        $user = User::factory()->create();

        // Create two mock calendar connections
        $sourceConnection = CalendarConnection::factory()->create([
            'user_id' => $user->id,
            'provider' => 'google',
            'status' => 'active',
        ]);

        $targetConnection = CalendarConnection::factory()->create([
            'user_id' => $user->id,
            'provider' => 'microsoft',
            'status' => 'active',
        ]);

        // Create sync rule
        $this->actingAs($user);
        
        $response = $this->post('/sync-rules', [
            'source_connection_id' => $sourceConnection->id,
            'source_calendar_id' => 'primary',
            'target_connections' => [
                ['connection_id' => $targetConnection->id, 'calendar_id' => 'calendar1'],
            ],
            'direction' => 'one_way',
            'blocker_title' => 'Busy — Test',
            'filters' => ['busy_only' => true],
        ]);

        $response->assertRedirect('/sync-rules');
        
        $this->assertDatabaseHas('sync_rules', [
            'user_id' => $user->id,
            'source_connection_id' => $sourceConnection->id,
            'blocker_title' => 'Busy — Test',
        ]);

        $this->assertDatabaseHas('sync_rule_targets', [
            'target_connection_id' => $targetConnection->id,
        ]);
    }

    public function test_free_users_cannot_create_multiple_rules()
    {
        $user = User::factory()->create(['subscription_tier' => 'free']);

        $sourceConnection = CalendarConnection::factory()->create([
            'user_id' => $user->id,
        ]);

        $targetConnection = CalendarConnection::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create first rule
        SyncRule::factory()->create([
            'user_id' => $user->id,
            'source_connection_id' => $sourceConnection->id,
        ]);

        $this->actingAs($user);

        // Try to create second rule
        $response = $this->post('/sync-rules', [
            'source_connection_id' => $sourceConnection->id,
            'source_calendar_id' => 'primary',
            'target_connections' => [
                ['connection_id' => $targetConnection->id, 'calendar_id' => 'calendar1'],
            ],
            'direction' => 'one_way',
            'blocker_title' => 'Busy',
        ]);

        // Should be redirected to billing
        $response->assertRedirect('/billing');
    }

    public function test_sync_rule_can_be_toggled()
    {
        $user = User::factory()->create();
        $rule = SyncRule::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post("/sync-rules/{$rule->id}/toggle");

        $response->assertRedirect();
        $this->assertFalse($rule->fresh()->is_active);
    }
}

