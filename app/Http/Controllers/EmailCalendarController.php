<?php

namespace App\Http\Controllers;

use App\Models\EmailCalendarConnection;
use App\Services\Email\EmailCalendarSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailCalendarController extends Controller
{
    public function __construct(
        private EmailCalendarSyncService $emailSync
    ) {}

    /**
     * Show email calendar connections
     */
    public function index()
    {
        // Email calendars are now shown on the main connections page
        return redirect()->route('connections.index');
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('email-calendars.create');
    }

    /**
     * Create new email calendar connection
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_email' => 'required|email|max:255',
            'description' => 'nullable|string|max:1000',
            'sender_whitelist' => 'nullable|string',
        ]);

        try {
            // Generate unique email address
            $emailData = EmailCalendarConnection::generateUniqueEmailAddress();

            // Parse sender whitelist (one email per line)
            $senderWhitelist = null;
            if (!empty($validated['sender_whitelist'])) {
                $emails = array_filter(
                    array_map('trim', explode("\n", $validated['sender_whitelist'])),
                    fn($email) => !empty($email)
                );
                $senderWhitelist = $emails;
            }

            // Create connection (without verified target_email)
            $connection = EmailCalendarConnection::create([
                'user_id' => auth()->id(),
                'email_address' => $emailData['email_address'],
                'email_token' => $emailData['email_token'],
                'name' => $validated['name'],
                'target_email' => $validated['target_email'],
                'description' => $validated['description'] ?? null,
                'sender_whitelist' => $senderWhitelist,
                'status' => 'active',
            ]);

            // Send verification email
            $connection->sendTargetEmailVerificationNotification();

            Log::info('Email calendar connection created', [
                'connection_id' => $connection->id,
                'email_address' => $connection->email_address,
                'target_email' => $connection->target_email,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('email-calendars.verification.notice', $connection)
                ->with('success', __('messages.email_calendar_created_verify'));

        } catch (\Exception $e) {
            Log::error('Failed to create email calendar connection', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('messages.email_calendar_creation_failed'));
        }
    }

    /**
     * Show edit form
     */
    public function edit(EmailCalendarConnection $emailCalendar)
    {
        $this->authorize('view', $emailCalendar);

        return view('email-calendars.edit', compact('emailCalendar'));
    }

    /**
     * Update email calendar connection
     */
    public function update(Request $request, EmailCalendarConnection $emailCalendar)
    {
        $this->authorize('update', $emailCalendar);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sender_whitelist' => 'nullable|string',
        ]);

        try {
            // Parse sender whitelist (one email per line)
            $senderWhitelist = null;
            if (!empty($validated['sender_whitelist'])) {
                $emails = array_filter(
                    array_map('trim', explode("\n", $validated['sender_whitelist'])),
                    fn($email) => !empty($email)
                );
                $senderWhitelist = $emails;
            }

            // Update connection
            $emailCalendar->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'sender_whitelist' => $senderWhitelist,
            ]);

            Log::info('Email calendar connection updated', [
                'connection_id' => $emailCalendar->id,
                'user_id' => auth()->id(),
                'name' => $validated['name'],
            ]);

            return redirect()->route('connections.index')
                ->with('success', __('messages.email_calendar_updated'));

        } catch (\Exception $e) {
            Log::error('Failed to update email calendar connection', [
                'connection_id' => $emailCalendar->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('messages.email_calendar_update_failed'));
        }
    }

    /**
     * Show single email calendar connection
     */
    public function show(EmailCalendarConnection $emailCalendar)
    {
        $this->authorize('view', $emailCalendar);

        // Get sync rules where this email calendar is source
        $syncRulesAsSource = \App\Models\SyncRule::where('source_email_connection_id', $emailCalendar->id)
            ->with(['targets.targetConnection', 'targets.targetEmailConnection'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get sync rules where this email calendar is target
        $syncRulesAsTarget = \App\Models\SyncRule::whereHas('targets', function($query) use ($emailCalendar) {
                $query->where('target_email_connection_id', $emailCalendar->id);
            })
            ->with(['sourceConnection', 'sourceEmailConnection', 'targets.targetConnection', 'targets.targetEmailConnection'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics - Received blockers (where this email calendar is target)
        $receivedBlockers = \App\Models\SyncEventMapping::where('target_email_connection_id', $emailCalendar->id)->count();

        // Statistics - Sent blockers (where this email calendar is source)
        $sentBlockers = \App\Models\SyncEventMapping::where('email_connection_id', $emailCalendar->id)->count();

        // Statistics - Last sync event
        $lastSyncEvent = \App\Models\SyncEventMapping::where(function($query) use ($emailCalendar) {
                $query->where('email_connection_id', $emailCalendar->id)
                      ->orWhere('target_email_connection_id', $emailCalendar->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        return view('email-calendars.show', [
            'emailCalendar' => $emailCalendar,
            'syncRulesAsSource' => $syncRulesAsSource,
            'syncRulesAsTarget' => $syncRulesAsTarget,
            'receivedBlockers' => $receivedBlockers,
            'sentBlockers' => $sentBlockers,
            'lastSyncEvent' => $lastSyncEvent,
        ]);
    }

    /**
     * Delete email calendar connection
     */
    public function destroy(EmailCalendarConnection $emailCalendar)
    {
        $this->authorize('delete', $emailCalendar);

        try {
            $emailCalendar->delete();

            Log::info('Email calendar connection deleted', [
                'connection_id' => $emailCalendar->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('connections.index')
                ->with('success', __('messages.email_calendar_deleted'));

        } catch (\Exception $e) {
            Log::error('Failed to delete email calendar connection', [
                'connection_id' => $emailCalendar->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', __('messages.connection_deleted_failed'));
        }
    }

    /**
     * Test email processing (for local development)
     */
    public function test(EmailCalendarConnection $emailCalendar)
    {
        $this->authorize('view', $emailCalendar);

        return view('email-calendars.test', compact('emailCalendar'));
    }

    /**
     * Process test email (for local development)
     */
    public function processTest(Request $request, EmailCalendarConnection $emailCalendar)
    {
        $this->authorize('view', $emailCalendar);

        $validated = $request->validate([
            'email_content' => 'required|string',
        ]);

        try {
            $result = $this->emailSync->processIncomingEmail(
                $emailCalendar->email_token,
                $validated['email_content']
            );

            if ($result['success']) {
                return back()->with('success', sprintf(
                    __('messages.email_processed_successfully'),
                    $result['events_processed'] ?? 0
                ));
            } else {
                return back()->with('error', $result['error'] ?? __('messages.email_processing_failed'));
            }

        } catch (\Exception $e) {
            Log::error('Test email processing failed', [
                'connection_id' => $emailCalendar->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }
}

