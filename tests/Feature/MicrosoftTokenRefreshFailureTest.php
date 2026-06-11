<?php

namespace Tests\Feature;

use App\Models\CalendarConnection;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MicrosoftTokenRefreshFailureTest extends TestCase
{
    use RefreshDatabase;

    private function expiredMicrosoftConnection(): CalendarConnection
    {
        return CalendarConnection::factory()->create([
            'provider' => 'microsoft',
            'status' => 'active',
            'token_expires_at' => now()->subHour(), // force refresh
        ]);
    }

    public function test_invalid_grant_marks_connection_expired(): void
    {
        Http::fake([
            'login.microsoftonline.com/*' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'AADSTS53003: Access has been blocked by Conditional Access policies.',
            ], 400),
        ]);

        $connection = $this->expiredMicrosoftConnection();

        try {
            (new MicrosoftCalendarService)->initializeWithConnection($connection);
            $this->fail('Expected token refresh to throw');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Token refresh failed', $e->getMessage());
        }

        $this->assertSame('expired', $connection->fresh()->status);
    }

    public function test_transient_error_marks_connection_error_not_expired(): void
    {
        Http::fake([
            'login.microsoftonline.com/*' => Http::response([
                'error' => 'interaction_required',
                'error_description' => 'Some non-permanent failure.',
            ], 400),
        ]);

        $connection = $this->expiredMicrosoftConnection();

        try {
            (new MicrosoftCalendarService)->initializeWithConnection($connection);
            $this->fail('Expected token refresh to throw');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Token refresh failed', $e->getMessage());
        }

        $this->assertSame('error', $connection->fresh()->status);
    }
}
