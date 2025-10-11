<?php

namespace App\Http\Controllers;

use App\Models\CalendarConnection;
use App\Services\Calendar\CalDavCalendarService;
use App\Services\Encryption\TokenEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalDavController extends Controller
{
    private TokenEncryptionService $encryptionService;
    
    public function __construct(TokenEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }
    
    /**
     * Show CalDAV setup form
     */
    public function showSetup()
    {
        return view('caldav.setup');
    }
    
    /**
     * Test CalDAV connection and show calendar selection
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'username' => 'required|string',
            'password' => 'required|string',
            'email' => 'nullable|email',
        ]);
        
        Log::info('Testing CalDAV connection', [
            'user_id' => auth()->id(),
            'url' => $validated['url'],
            'username' => $validated['username'],
        ]);
        
        // Test connection
        $result = CalDavCalendarService::testConnection(
            $validated['url'],
            $validated['username'],
            $validated['password']
        );
        
        if (!$result['success']) {
            return back()
                ->withInput()
                ->with('error', __('messages.caldav_connection_failed') . ': ' . $result['error']);
        }
        
        // Store credentials in session for calendar selection
        session([
            'caldav_url' => $validated['url'],
            'caldav_username' => $validated['username'],
            'caldav_password' => $validated['password'],
            'caldav_email' => $validated['email'] ?? $validated['username'],
            'caldav_principal_url' => $result['calendar_home_url'] ?? $result['principal_url'],
            'caldav_calendars' => $result['calendars'],
        ]);
        
        return view('caldav.select-calendars', [
            'calendars' => $result['calendars'],
            'email' => $validated['email'] ?? $validated['username'],
        ]);
    }
    
    /**
     * Complete CalDAV setup and save connection
     */
    public function complete(Request $request)
    {
        $validated = $request->validate([
            'selected_calendars' => 'required|array|min:1',
            'selected_calendars.*' => 'required|string',
        ]);
        
        // Get credentials from session
        $url = session('caldav_url');
        $username = session('caldav_username');
        $password = session('caldav_password');
        $email = session('caldav_email');
        $principalUrl = session('caldav_principal_url');
        $allCalendars = session('caldav_calendars', []);
        
        if (!$url || !$username || !$password) {
            return redirect()->route('caldav.setup')
                ->with('error', __('messages.session_expired'));
        }
        
        try {
            // Encrypt password
            $encryptedPassword = $this->encryptionService->encrypt($password);
            
            // Filter selected calendars
            $selectedCalendars = array_filter($allCalendars, function ($calendar) use ($validated) {
                return in_array($calendar['id'], $validated['selected_calendars']);
            });
            
            // Create connection
            $connection = CalendarConnection::create([
                'user_id' => auth()->id(),
                'provider' => 'caldav',
                'provider_account_id' => $username,
                'provider_email' => $email,
                'account_email' => $email,
                'caldav_url' => $url,
                'caldav_username' => $username,
                'caldav_password_encrypted' => $encryptedPassword,
                'caldav_principal_url' => $principalUrl,
                'available_calendars' => array_values($selectedCalendars),
                'status' => 'active',
            ]);
            
            Log::info('CalDAV connection created', [
                'connection_id' => $connection->id,
                'user_id' => auth()->id(),
                'url' => $url,
                'calendar_count' => count($selectedCalendars),
            ]);
            
            // Clear session
            session()->forget(['caldav_url', 'caldav_username', 'caldav_password', 'caldav_email', 'caldav_principal_url', 'caldav_calendars']);
            
            return redirect()->route('connections.index')
                ->with('success', __('messages.caldav_connected_success'));
                
        } catch (\Exception $e) {
            Log::error('Failed to create CalDAV connection', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            
            return back()
                ->with('error', __('messages.caldav_connection_save_failed'));
        }
    }
    
    /**
     * Disconnect CalDAV calendar
     */
    public function disconnect(CalendarConnection $connection)
    {
        // Check permission
        if ($connection->user_id !== auth()->id()) {
            abort(403);
        }
        
        if ($connection->provider !== 'caldav') {
            abort(400, 'Not a CalDAV connection');
        }
        
        Log::info('Disconnecting CalDAV connection', [
            'connection_id' => $connection->id,
            'user_id' => auth()->id(),
        ]);
        
        $connection->delete();
        
        return redirect()->route('connections.index')
            ->with('success', __('messages.caldav_disconnected_success'));
    }
}
