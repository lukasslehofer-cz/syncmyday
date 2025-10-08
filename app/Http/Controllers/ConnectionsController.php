<?php

namespace App\Http\Controllers;

use App\Models\CalendarConnection;
use App\Models\EmailCalendarConnection;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConnectionsController extends Controller
{
    /**
     * Show all calendar connections
     */
    public function index()
    {
        $connections = auth()->user()
            ->calendarConnections()
            ->orderBy('created_at', 'desc')
            ->get();

        $emailCalendars = EmailCalendarConnection::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('connections.index', compact('connections', 'emailCalendars'));
    }

    /**
     * Delete a calendar connection
     */
    public function destroy(CalendarConnection $connection)
    {
        // Authorization check
        if ($connection->user_id !== auth()->id()) {
            abort(403);
        }

        // Stop all webhook subscriptions for this connection
        foreach ($connection->webhookSubscriptions as $subscription) {
            try {
                if ($connection->provider === 'google') {
                    $service = app(GoogleCalendarService::class);
                    $service->initializeWithConnection($connection);
                    $service->stopWebhook($subscription->provider_subscription_id, $subscription->resource_id);
                } else {
                    $service = app(MicrosoftCalendarService::class);
                    $service->initializeWithConnection($connection);
                    $service->stopWebhook($subscription->provider_subscription_id);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to stop webhook during connection deletion', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $provider = $connection->provider;
        $connection->delete();

        Log::info('Calendar connection deleted', [
            'user_id' => auth()->id(),
            'provider' => $provider,
        ]);

        return redirect()->route('connections.index')
            ->with('success', __('messages.connection_deleted'));
    }

    /**
     * Refresh connection (re-fetch calendars)
     */
    public function refresh(CalendarConnection $connection)
    {
        // Authorization check
        if ($connection->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            if ($connection->provider === 'google') {
                $service = app(GoogleCalendarService::class);
            } else {
                $service = app(MicrosoftCalendarService::class);
            }

            $service->initializeWithConnection($connection);
            $calendars = $service->getCalendarList();

            $connection->update([
                'available_calendars' => $calendars,
                'status' => 'active',
                'last_error' => null,
            ]);

            return redirect()->route('connections.index')
                ->with('success', __('messages.connection_refreshed'));

        } catch (\Exception $e) {
            Log::error('Connection refresh failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            $connection->update([
                'status' => 'error',
                'last_error' => $e->getMessage(),
            ]);

            return redirect()->route('connections.index')
                ->with('error', __('messages.connection_refresh_failed'));
        }
    }
}

