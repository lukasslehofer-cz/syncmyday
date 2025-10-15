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
        $providerType = $request->input('provider_type', 'other');
        
        Log::info('CalDAV testConnection called', [
            'user_id' => auth()->id(),
            'provider_type' => $providerType,
            'has_apple_id' => $request->has('apple_id'),
            'has_app_password' => $request->has('app_password'),
            'has_url' => $request->has('url'),
        ]);
        
        if ($providerType === 'icloud') {
            // Apple iCloud - automatic discovery
            $validated = $request->validate([
                'apple_id' => 'required|email',
                'app_password' => 'required|string',
            ]);
            
            Log::info('Testing Apple iCloud connection (auto-discovery)', [
                'user_id' => auth()->id(),
                'apple_id' => $validated['apple_id'],
            ]);
            
            try {
                // Use iCloud discovery
                $result = CalDavCalendarService::discoverICloud(
                    $validated['apple_id'],
                    $validated['app_password']
                );
                
                if (!$result['success']) {
                    Log::warning('iCloud connection failed', [
                        'user_id' => auth()->id(),
                        'error' => $result['error'],
                    ]);
                    
                    return back()
                        ->withInput()
                        ->with('error', $result['error'] ?? __('messages.caldav_connection_failed'));
                }
                
                // Store credentials in session for calendar selection
                session([
                    'caldav_url' => $result['url'],
                    'caldav_username' => $validated['apple_id'],
                    'caldav_password' => $validated['app_password'],
                    'caldav_email' => $validated['apple_id'],
                    'caldav_principal_url' => $result['calendar_home_url'] ?? $result['principal_url'],
                    'caldav_calendars' => $result['calendars'],
                ]);

                return redirect()->route('caldav.select-calendars');
                
            } catch (\Exception $e) {
                Log::error('Unexpected error during iCloud connection', [
                    'user_id' => auth()->id(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                return back()
                    ->withInput()
                    ->with('error', 'Unexpected error: ' . $e->getMessage());
            }
            
        } else {
            // Other CalDAV - manual configuration
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
            
            try {
                // Test connection
                $result = CalDavCalendarService::testConnection(
                    $validated['url'],
                    $validated['username'],
                    $validated['password']
                );
                
                if (!$result['success']) {
                    Log::warning('CalDAV connection failed', [
                        'user_id' => auth()->id(),
                        'url' => $validated['url'],
                        'error' => $result['error'],
                    ]);
                    
                    return back()
                        ->withInput()
                        ->with('error', $result['error'] ?? __('messages.caldav_connection_failed'));
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
                
                return redirect()->route('caldav.select-calendars');
                
            } catch (\Exception $e) {
                Log::error('Unexpected error during CalDAV connection', [
                    'user_id' => auth()->id(),
                    'url' => $validated['url'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                return back()
                    ->withInput()
                    ->with('error', 'Unexpected error: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Show calendar selection page
     */
    public function showSelectCalendars()
    {
        // Get data from session
        $calendars = session('caldav_calendars', []);
        $email = session('caldav_email');
        
        if (!$email) {
            return redirect()->route('caldav.setup')
                ->with('error', __('messages.session_expired'));
        }
        
        return view('caldav.select-calendars', [
            'calendars' => $calendars,
            'email' => $email,
        ]);
    }
    
    /**
     * Complete CalDAV setup and save connection
     */
    public function complete(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'selected_calendar_id' => 'required|string',
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
            
            // Create connection
            $connection = CalendarConnection::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'provider' => 'caldav',
                'provider_account_id' => $username,
                'provider_email' => $email,
                'account_email' => $email,
                'caldav_url' => $url,
                'caldav_username' => $username,
                'caldav_password_encrypted' => $encryptedPassword,
                'caldav_principal_url' => $principalUrl,
                'available_calendars' => $allCalendars,
                'selected_calendar_id' => $validated['selected_calendar_id'],
                'status' => 'active',
            ]);
            
            Log::info('CalDAV connection created', [
                'connection_id' => $connection->id,
                'user_id' => auth()->id(),
                'url' => $url,
                'name' => $validated['name'],
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
