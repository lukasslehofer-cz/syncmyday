<?php

namespace Tests\Feature;

use App\Mail\CalendarConnectionExpiredMail;
use App\Models\CalendarConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ConnectionsCheckNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_reconnection_email_once_for_expired_connection(): void
    {
        Mail::fake();

        $user = User::factory()->create(['locale' => 'en']);
        $connection = CalendarConnection::factory()->create([
            'user_id' => $user->id,
            'provider' => 'microsoft',
            'status' => 'expired',
            'reconnection_email_sent_at' => null,
        ]);

        $this->artisan('connections:check')->assertExitCode(0);

        Mail::assertSent(CalendarConnectionExpiredMail::class, 1);
        $this->assertNotNull($connection->fresh()->reconnection_email_sent_at);

        // Second run must not re-send: the timestamp is already set.
        $this->artisan('connections:check')->assertExitCode(0);
        Mail::assertSent(CalendarConnectionExpiredMail::class, 1);
    }

    public function test_does_not_email_active_connections(): void
    {
        Mail::fake();

        $user = User::factory()->create(['locale' => 'en']);
        CalendarConnection::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $this->artisan('connections:check')->assertExitCode(0);

        Mail::assertNothingSent();
    }
}
