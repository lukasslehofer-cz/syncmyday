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
            'target_email' => 'nullable|email|max:255',
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

            // Create connection
            $connection = EmailCalendarConnection::create([
                'user_id' => auth()->id(),
                'email_address' => $emailData['email_address'],
                'email_token' => $emailData['email_token'],
                'name' => $validated['name'],
                'target_email' => $validated['target_email'] ?? null,
                'description' => $validated['description'] ?? null,
                'sender_whitelist' => $senderWhitelist,
                'status' => 'active',
            ]);

            Log::info('Email calendar connection created', [
                'connection_id' => $connection->id,
                'email_address' => $connection->email_address,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('email-calendars.show', $connection)
                ->with('success', __('messages.email_calendar_created'));

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
     * Show single email calendar connection
     */
    public function show(EmailCalendarConnection $emailCalendar)
    {
        $this->authorize('view', $emailCalendar);

        return view('email-calendars.show', compact('emailCalendar'));
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

