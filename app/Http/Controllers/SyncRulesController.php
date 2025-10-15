<?php

namespace App\Http\Controllers;

use App\Models\CalendarConnection;
use App\Models\EmailCalendarConnection;
use App\Models\SyncRule;
use App\Models\SyncRuleTarget;
use App\Models\WebhookSubscription;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SyncRulesController extends Controller
{
    /**
     * List all sync rules
     */
    public function index()
    {
        $rules = auth()->user()
            ->syncRules()
            ->with([
                'sourceConnection',
                'sourceEmailConnection',
                'targets.targetConnection',
                'targets.targetEmailConnection'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sync-rules.index', compact('rules'));
    }

    /**
     * Show form to create new sync rule
     */
    public function create()
    {
        // Check if user can create more rules
        if (!auth()->user()->canCreateSyncRule()) {
            return redirect()->route('sync-rules.index')
                ->with('error', __('messages.sync_rule_limit_reached'));
        }

        $apiConnections = auth()->user()
            ->calendarConnections()
            ->where('status', 'active')
            ->get();
        
        $emailConnections = auth()->user()
            ->emailCalendarConnections()
            ->where('status', 'active')
            ->get();

        $totalConnections = $apiConnections->count() + $emailConnections->count();

        if ($totalConnections < 2) {
            return redirect()->route('connections.index')
                ->with('error', __('messages.need_two_calendars'));
        }

        return view('sync-rules.create', compact('apiConnections', 'emailConnections'));
    }

    /**
     * Store new sync rule
     */
    public function store(Request $request)
    {
        // Check permission
        if (!Gate::allows('create-sync-rule')) {
            return redirect()->route('billing')
                ->with('error', __('messages.subscription_required'));
        }

        Log::info('Sync rule store attempt', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'source_type_and_id' => 'required|string',
            'target_connections' => 'required|array|min:1',
            'target_connections.*.type_and_id' => 'required|string',
            'direction' => 'required|in:one_way,two_way',
            'blocker_title' => 'nullable|string|max:100',
            'filters' => 'nullable|array',
            'time_filter_enabled' => 'nullable|boolean',
            'time_filter_type' => 'nullable|in:workdays,weekends,custom',
            'workdays_time_start' => 'nullable|date_format:H:i',
            'workdays_time_end' => 'nullable|date_format:H:i',
            'time_filter_start' => 'nullable|date_format:H:i',
            'time_filter_end' => 'nullable|date_format:H:i',
            'time_filter_days' => 'nullable|array',
            'time_filter_days.*' => 'integer|between:1,7',
        ]);

        // Parse source connection
        list($sourceType, $sourceId) = explode('-', $validated['source_type_and_id'], 2);
        $sourceConnectionId = null;
        $sourceEmailConnectionId = null;
        $sourceCalendarId = null;
        
        if ($sourceType === 'api') {
            $sourceConnection = CalendarConnection::findOrFail($sourceId);
            if ($sourceConnection->user_id !== auth()->id()) {
                abort(403);
            }
            $sourceConnectionId = $sourceConnection->id;
            $sourceCalendarId = $sourceConnection->selected_calendar_id;
        } else {
            $sourceEmailConnection = EmailCalendarConnection::findOrFail($sourceId);
            if ($sourceEmailConnection->user_id !== auth()->id()) {
                abort(403);
            }
            $sourceEmailConnectionId = $sourceEmailConnection->id;
        }
        
        // Parse target connections
        $processedTargets = [];
        foreach ($validated['target_connections'] as $targetData) {
            list($targetType, $targetId) = explode('-', $targetData['type_and_id'], 2);
            
            if ($targetType === 'api') {
                $targetConnection = CalendarConnection::findOrFail($targetId);
                if ($targetConnection->user_id !== auth()->id()) {
                    abort(403);
                }
                $processedTargets[] = [
                    'type' => 'api',
                    'connection_id' => $targetConnection->id,
                    'email_connection_id' => null,
                    'calendar_id' => $targetConnection->selected_calendar_id,
                ];
            } else {
                $targetEmailConnection = EmailCalendarConnection::findOrFail($targetId);
                if ($targetEmailConnection->user_id !== auth()->id()) {
                    abort(403);
                }
                $processedTargets[] = [
                    'type' => 'email',
                    'connection_id' => null,
                    'email_connection_id' => $targetEmailConnection->id,
                    'calendar_id' => null,
                ];
            }
        }

        try {
            DB::beginTransaction();

            // Process time filter based on type
            $timeFilterEnabled = $validated['time_filter_enabled'] ?? false;
            $timeFilterType = $validated['time_filter_type'] ?? null;
            $timeFilterStart = null;
            $timeFilterEnd = null;
            $timeFilterDays = null;

            if ($timeFilterEnabled && $timeFilterType) {
                if ($timeFilterType === 'workdays') {
                    // Monday-Friday with custom time range (default 8:00-18:00)
                    $timeFilterDays = [1, 2, 3, 4, 5];
                    $timeFilterStart = $request->input('workdays_time_start', '08:00') . ':00';
                    $timeFilterEnd = $request->input('workdays_time_end', '18:00') . ':00';
                } elseif ($timeFilterType === 'weekends') {
                    // Saturday-Sunday, all day
                    $timeFilterDays = [6, 7];
                    $timeFilterStart = '00:00:00';
                    $timeFilterEnd = '23:59:59';
                } elseif ($timeFilterType === 'custom') {
                    // Use custom values from form
                    $timeFilterDays = $validated['time_filter_days'] ?? null;
                    $timeFilterStart = $validated['time_filter_start'] ? $validated['time_filter_start'] . ':00' : null;
                    $timeFilterEnd = $validated['time_filter_end'] ? $validated['time_filter_end'] . ':00' : null;
                }
            }

            // Create sync rule (forward direction)
            $rule = SyncRule::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'source_connection_id' => $sourceConnectionId,
                'source_email_connection_id' => $sourceEmailConnectionId,
                'source_calendar_id' => $sourceCalendarId,
                'direction' => $validated['direction'],
                'blocker_title' => $validated['blocker_title'] ?? 'Busy — Sync',
                'filters' => $validated['filters'] ?? [
                    'busy_only' => true,
                    'ignore_all_day' => false,
                ],
                'time_filter_enabled' => $timeFilterEnabled,
                'time_filter_type' => $timeFilterType,
                'time_filter_start' => $timeFilterStart,
                'time_filter_end' => $timeFilterEnd,
                'time_filter_days' => $timeFilterDays,
                'is_active' => true,
                'initial_sync_completed' => false, // Will be set to true after first sync
            ]);

            // Create targets for forward rule
            foreach ($processedTargets as $target) {
                SyncRuleTarget::create([
                    'sync_rule_id' => $rule->id,
                    'target_connection_id' => $target['connection_id'],
                    'target_email_connection_id' => $target['email_connection_id'],
                    'target_calendar_id' => $target['calendar_id'],
                ]);
            }

            // For bidirectional sync, create reverse rules
            if ($validated['direction'] === 'two_way') {
                foreach ($processedTargets as $target) {
                    // Get target name for reverse rule name
                    $targetName = '';
                    if ($target['connection_id']) {
                        $targetConn = CalendarConnection::find($target['connection_id']);
                        $targetName = $targetConn->name ?? $targetConn->provider_email;
                    } else {
                        $targetEmail = EmailCalendarConnection::find($target['email_connection_id']);
                        $targetName = $targetEmail->name;
                    }
                    
                    // Create reverse rule: target -> source
                    $reverseRule = SyncRule::create([
                        'user_id' => auth()->id(),
                        'name' => $validated['name'] . ' (' . $targetName . ' → reverse)',
                        'source_connection_id' => $target['connection_id'],
                        'source_email_connection_id' => $target['email_connection_id'],
                        'source_calendar_id' => $target['calendar_id'],
                        'direction' => 'two_way', // Both forward and reverse use 'two_way'
                        'blocker_title' => $validated['blocker_title'] ?? 'Busy — Sync',
                        'filters' => $validated['filters'] ?? [
                            'busy_only' => true,
                            'ignore_all_day' => false,
                        ],
                        'time_filter_enabled' => $timeFilterEnabled,
                        'time_filter_type' => $timeFilterType,
                        'time_filter_start' => $timeFilterStart,
                        'time_filter_end' => $timeFilterEnd,
                        'time_filter_days' => $timeFilterDays,
                        'is_active' => true,
                        'initial_sync_completed' => false, // Will be set to true after first sync
                    ]);

                    // Create target for reverse rule (original source)
                    SyncRuleTarget::create([
                        'sync_rule_id' => $reverseRule->id,
                        'target_connection_id' => $sourceConnectionId,
                        'target_email_connection_id' => $sourceEmailConnectionId,
                        'target_calendar_id' => $sourceCalendarId,
                    ]);

                    // Create webhook subscription for reverse rule (only for API calendars)
                    if ($target['connection_id']) {
                        $targetConnection = CalendarConnection::find($target['connection_id']);
                        if ($targetConnection) {
                            $this->ensureWebhookSubscription($targetConnection, $target['calendar_id']);
                        }
                    }
                }
            }

            // Create webhook subscription for source calendar (only for API calendars)
            if ($sourceConnectionId && $rule->sourceConnection) {
                $this->ensureWebhookSubscription(
                    $rule->sourceConnection,
                    $sourceCalendarId
                );
            }

            DB::commit();

            Log::info('Sync rule created', [
                'user_id' => auth()->id(),
                'rule_id' => $rule->id,
            ]);

            // Trigger initial sync immediately (only for API calendars)
            if ($validated['source_type'] === 'api' && $rule->sourceConnection) {
                try {
                    $syncEngine = app(\App\Services\Sync\SyncEngine::class);
                    $syncEngine->syncRule($rule, $rule->sourceConnection);
                    
                    Log::info('Initial sync triggered for new rule', [
                        'rule_id' => $rule->id,
                    ]);
                } catch (\Exception $e) {
                    // Don't fail rule creation if initial sync fails
                    Log::warning('Initial sync failed for new rule', [
                        'rule_id' => $rule->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->route('sync-rules.index')
                ->with('success', __('messages.sync_rule_created'));

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create sync rule', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('error', __('messages.sync_rule_creation_failed'))
                ->withInput();
        }
    }

    /**
     * Show form to edit sync rule
     */
    public function edit(SyncRule $rule)
    {
        // Authorization
        if ($rule->user_id !== auth()->id()) {
            abort(403);
        }

        $apiConnections = auth()->user()
            ->calendarConnections()
            ->where('status', 'active')
            ->get();
        
        $emailConnections = auth()->user()
            ->emailCalendarConnections()
            ->where('status', 'active')
            ->get();

        // Load relationships
        $rule->load(['sourceConnection', 'sourceEmailConnection', 'targets.targetConnection', 'targets.targetEmailConnection']);

        return view('sync-rules.edit', compact('rule', 'apiConnections', 'emailConnections'));
    }

    /**
     * Update sync rule
     */
    public function update(Request $request, SyncRule $rule)
    {
        // Authorization
        if ($rule->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'blocker_title' => 'nullable|string|max:100',
            'filters' => 'nullable|array',
            'time_filter_enabled' => 'nullable|boolean',
            'time_filter_type' => 'nullable|in:workdays,weekends,custom',
            'workdays_time_start' => 'nullable|date_format:H:i',
            'workdays_time_end' => 'nullable|date_format:H:i',
            'time_filter_start' => 'nullable|date_format:H:i',
            'time_filter_end' => 'nullable|date_format:H:i',
            'time_filter_days' => 'nullable|array',
            'time_filter_days.*' => 'integer|between:1,7',
        ]);

        try {
            // Process time filter based on type
            $timeFilterEnabled = $validated['time_filter_enabled'] ?? false;
            $timeFilterType = $validated['time_filter_type'] ?? null;
            $timeFilterStart = null;
            $timeFilterEnd = null;
            $timeFilterDays = null;

            if ($timeFilterEnabled && $timeFilterType) {
                if ($timeFilterType === 'workdays') {
                    $timeFilterDays = [1, 2, 3, 4, 5];
                    $timeFilterStart = $request->input('workdays_time_start', '08:00') . ':00';
                    $timeFilterEnd = $request->input('workdays_time_end', '18:00') . ':00';
                } elseif ($timeFilterType === 'weekends') {
                    $timeFilterDays = [6, 7];
                    $timeFilterStart = '00:00:00';
                    $timeFilterEnd = '23:59:59';
                } elseif ($timeFilterType === 'custom') {
                    $timeFilterDays = $validated['time_filter_days'] ?? null;
                    $timeFilterStart = $validated['time_filter_start'] ? $validated['time_filter_start'] . ':00' : null;
                    $timeFilterEnd = $validated['time_filter_end'] ? $validated['time_filter_end'] . ':00' : null;
                }
            }

            $rule->update([
                'name' => $validated['name'],
                'blocker_title' => $validated['blocker_title'] ?? 'Busy — Sync',
                'filters' => $validated['filters'] ?? [
                    'busy_only' => true,
                    'ignore_all_day' => false,
                ],
                'time_filter_enabled' => $timeFilterEnabled,
                'time_filter_type' => $timeFilterType,
                'time_filter_start' => $timeFilterStart,
                'time_filter_end' => $timeFilterEnd,
                'time_filter_days' => $timeFilterDays,
            ]);

            Log::info('Sync rule updated', [
                'user_id' => auth()->id(),
                'rule_id' => $rule->id,
            ]);

            return redirect()->route('sync-rules.index')
                ->with('success', __('messages.sync_rule_updated'));

        } catch (\Exception $e) {
            Log::error('Failed to update sync rule', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'rule_id' => $rule->id,
            ]);

            return redirect()->back()
                ->with('error', __('messages.sync_rule_update_failed'))
                ->withInput();
        }
    }

    /**
     * Toggle sync rule active status
     */
    public function toggle(SyncRule $rule)
    {
        // Authorization
        if ($rule->user_id !== auth()->id()) {
            abort(403);
        }

        $rule->update(['is_active' => !$rule->is_active]);

        return redirect()->back()
            ->with('success', __('messages.sync_rule_updated'));
    }

    /**
     * Delete sync rule
     */
    public function destroy(SyncRule $rule)
    {
        // Authorization
        if ($rule->user_id !== auth()->id()) {
            abort(403);
        }

        $rule->delete();

        Log::info('Sync rule deleted', [
            'user_id' => auth()->id(),
            'rule_id' => $rule->id,
        ]);

        return redirect()->route('sync-rules.index')
            ->with('success', __('messages.sync_rule_deleted'));
    }

    /**
     * Ensure webhook subscription exists for calendar
     */
    private function ensureWebhookSubscription(CalendarConnection $connection, string $calendarId): void
    {
        // CalDAV doesn't support webhooks - it uses polling instead
        if ($connection->provider === 'caldav') {
            Log::info('Skipping webhook creation for CalDAV (uses polling)', [
                'connection_id' => $connection->id,
                'calendar_id' => $calendarId,
            ]);
            return;
        }
        
        // Skip webhooks for localhost (requires HTTPS)
        $appUrl = config('app.url');
        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            Log::info('Skipping webhook creation for localhost', [
                'connection_id' => $connection->id,
                'calendar_id' => $calendarId,
                'note' => 'Webhooks require HTTPS. Use ngrok or deploy to production for real-time sync.',
            ]);
            return;
        }

        // Check if subscription already exists
        $existing = WebhookSubscription::where('calendar_connection_id', $connection->id)
            ->where('calendar_id', $calendarId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return; // Already have active subscription
        }

        // Try to create new subscription
        try {
            $webhookUrl = config('app.url') . "/webhooks/{$connection->provider}/{$connection->id}";

            if ($connection->provider === 'google') {
                $service = app(GoogleCalendarService::class);
            } else {
                $service = app(MicrosoftCalendarService::class);
            }

            $service->initializeWithConnection($connection);
            $subscriptionData = $service->createWebhook($calendarId, $webhookUrl);

            WebhookSubscription::create([
                'calendar_connection_id' => $connection->id,
                'provider_subscription_id' => $subscriptionData['subscription_id'],
                'resource_id' => $subscriptionData['resource_id'],
                'calendar_id' => $calendarId,
                'expires_at' => $subscriptionData['expires_at'],
                'status' => 'active',
            ]);

            Log::info('Webhook subscription created', [
                'connection_id' => $connection->id,
                'calendar_id' => $calendarId,
            ]);
        } catch (\Exception $e) {
            // Webhook creation failed - log but don't fail the whole rule creation
            Log::warning('Failed to create webhook subscription', [
                'connection_id' => $connection->id,
                'calendar_id' => $calendarId,
                'error' => $e->getMessage(),
                'note' => 'Sync will work via polling instead of real-time webhooks',
            ]);
        }
    }
}

