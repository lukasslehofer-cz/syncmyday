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
     * Show calendar connection details
     */
    public function show(CalendarConnection $connection)
    {
        // Authorization check
        if ($connection->user_id !== auth()->id()) {
            abort(403);
        }

        // Get selected calendar details
        $selectedCalendar = null;
        if ($connection->selected_calendar_id && $connection->available_calendars) {
            foreach ($connection->available_calendars as $calendar) {
                if ($calendar['id'] === $connection->selected_calendar_id) {
                    $selectedCalendar = $calendar;
                    break;
                }
            }
        }

        // Get sync rules where this connection is source
        $syncRulesAsSource = \App\Models\SyncRule::where('source_connection_id', $connection->id)
            ->with(['targets.targetConnection', 'targets.targetEmailConnection'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get sync rules where this connection is target
        $syncRulesAsTarget = \App\Models\SyncRule::whereHas('targets', function($query) use ($connection) {
                $query->where('target_connection_id', $connection->id);
            })
            ->with(['sourceConnection', 'sourceEmailConnection', 'targets.targetConnection', 'targets.targetEmailConnection'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics - Received blockers (where this connection is target)
        $receivedBlockers = \App\Models\SyncEventMapping::where('target_connection_id', $connection->id)->count();

        // Statistics - Sent blockers (where this connection is source)
        $sentBlockers = \App\Models\SyncEventMapping::where('source_connection_id', $connection->id)->count();

        // Statistics - Last sync event
        $lastSyncEvent = \App\Models\SyncEventMapping::where(function($query) use ($connection) {
                $query->where('source_connection_id', $connection->id)
                      ->orWhere('target_connection_id', $connection->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        return view('connections.show', [
            'connection' => $connection,
            'selectedCalendar' => $selectedCalendar,
            'syncRulesAsSource' => $syncRulesAsSource,
            'syncRulesAsTarget' => $syncRulesAsTarget,
            'receivedBlockers' => $receivedBlockers,
            'sentBlockers' => $sentBlockers,
            'lastSyncEvent' => $lastSyncEvent,
        ]);
    }

    /**
     * Show edit form for calendar connection
     */
    public function edit(CalendarConnection $connection)
    {
        // Authorization check
        if ($connection->user_id !== auth()->id()) {
            abort(403);
        }

        // Find primary calendar if selected_calendar_id not set
        $selectedCalendarId = $connection->selected_calendar_id;
        if (!$selectedCalendarId && $connection->available_calendars) {
            foreach ($connection->available_calendars as $calendar) {
                if ($calendar['primary'] ?? false) {
                    $selectedCalendarId = $calendar['id'];
                    break;
                }
            }
            // If no primary found, use first calendar
            if (!$selectedCalendarId && count($connection->available_calendars) > 0) {
                $selectedCalendarId = $connection->available_calendars[0]['id'];
            }
        }

        return view('connections.edit', [
            'connection' => $connection,
            'selectedCalendarId' => $selectedCalendarId,
        ]);
    }

    /**
     * Update calendar connection
     */
    public function update(Request $request, CalendarConnection $connection)
    {
        // Authorization check
        if ($connection->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'selected_calendar_id' => 'required|string',
        ]);

        try {
            $connection->update([
                'name' => $validated['name'],
                'selected_calendar_id' => $validated['selected_calendar_id'],
            ]);

            Log::info('Calendar connection updated', [
                'connection_id' => $connection->id,
                'user_id' => auth()->id(),
                'name' => $validated['name'],
            ]);

            return redirect()->route('connections.index')
                ->with('success', __('messages.connection_updated'));

        } catch (\Exception $e) {
            Log::error('Failed to update calendar connection', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('messages.connection_update_failed'));
        }
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

        // Log webhook subscriptions before attempting to stop them
        $webhookSubscriptions = $connection->webhookSubscriptions()->get();
        $webhookCount = $webhookSubscriptions->count();
        
        Log::info('Stopping webhook subscriptions before deleting connection', [
            'connection_id' => $connection->id,
            'provider' => $connection->provider,
            'webhook_count' => $webhookCount,
            'subscriptions' => $webhookSubscriptions->map(function($sub) {
                return [
                    'id' => $sub->id,
                    'channel_id' => $sub->provider_subscription_id,
                    'resource_id' => $sub->resource_id,
                    'calendar_id' => $sub->calendar_id,
                    'status' => $sub->status,
                    'expires_at' => $sub->expires_at,
                ];
            })->toArray(),
        ]);
        
        if ($webhookCount === 0) {
            Log::warning('No webhook subscriptions found in database - channel may still be active on provider side', [
                'connection_id' => $connection->id,
                'provider' => $connection->provider,
                'provider_email' => $connection->provider_email,
            ]);
        }

        // Stop all webhook subscriptions for this connection
        $stoppedCount = 0;
        $failedCount = 0;
        
        foreach ($webhookSubscriptions as $subscription) {
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
                
                $stoppedCount++;
                Log::info('Webhook stopped successfully', [
                    'connection_id' => $connection->id,
                    'subscription_id' => $subscription->id,
                    'channel_id' => $subscription->provider_subscription_id,
                    'calendar_id' => $subscription->calendar_id,
                ]);
                
            } catch (\Exception $e) {
                $failedCount++;
                Log::warning('Failed to stop webhook during connection deletion', [
                    'connection_id' => $connection->id,
                    'subscription_id' => $subscription->id,
                    'channel_id' => $subscription->provider_subscription_id,
                    'resource_id' => $subscription->resource_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
        
        Log::info('Webhook cleanup completed', [
            'connection_id' => $connection->id,
            'total' => $webhookCount,
            'stopped' => $stoppedCount,
            'failed' => $failedCount,
        ]);

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

