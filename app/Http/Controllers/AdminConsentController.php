<?php

namespace App\Http\Controllers;

use App\Models\CalendarConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminConsentController extends Controller
{
    /**
     * Initiate Microsoft admin consent flow
     */
    public function initiate()
    {
        $clientId = config('services.microsoft.client_id');
        $tenant = config('services.microsoft.tenant', 'common');
        
        // For admin consent, we need to use 'organizations' instead of 'common'
        // to target work/school accounts specifically
        if ($tenant === 'common') {
            $tenant = 'organizations';
        }
        
        $redirectUri = $this->getCurrentDomainUrl('/admin-consent/microsoft/callback');
        $scopes = implode(' ', config('services.microsoft.scopes'));
        
        // Build admin consent URL
        $adminConsentUrl = sprintf(
            'https://login.microsoftonline.com/%s/v2.0/adminconsent?client_id=%s&redirect_uri=%s&scope=%s&state=%s',
            $tenant,
            $clientId,
            urlencode($redirectUri),
            urlencode($scopes),
            csrf_token()
        );
        
        Log::info('Microsoft Admin Consent - Initiated', [
            'user_id' => auth()->id(),
            'redirect_uri' => $redirectUri,
        ]);
        
        return redirect($adminConsentUrl);
    }
    
    /**
     * Handle admin consent callback
     */
    public function callback(Request $request)
    {
        $connectionId = $request->state; // We passed connection ID as state
        
        Log::info('Microsoft Admin Consent - Callback received', [
            'has_admin_consent' => $request->has('admin_consent'),
            'has_tenant' => $request->has('tenant'),
            'has_error' => $request->has('error'),
            'connection_id' => $connectionId,
            'user_id' => auth()->id(),
        ]);
        
        // Check if admin consent was granted
        if ($request->has('error')) {
            Log::warning('Microsoft Admin Consent - Error', [
                'error' => $request->error,
                'error_description' => $request->error_description,
            ]);
            
            return redirect()->route('connections.index')
                ->with('error', 'Admin consent was not granted: ' . ($request->error_description ?? $request->error));
        }
        
        if ($request->admin_consent === 'True' && $request->has('tenant')) {
            $tenantId = $request->tenant;
            
            Log::info('Microsoft Admin Consent - Granted', [
                'tenant_id' => $tenantId,
                'connection_id' => $connectionId,
                'user_id' => auth()->id(),
            ]);
            
            // Delete the old connection (it has invalid token without approved scopes)
            if ($connectionId) {
                $connection = CalendarConnection::find($connectionId);
                if ($connection && $connection->user_id === auth()->id()) {
                    Log::info('Microsoft Admin Consent - Deleting old connection with unapproved token', [
                        'connection_id' => $connection->id,
                    ]);
                    $connection->delete();
                }
            }
            
            // Redirect back to OAuth flow to get a NEW token with approved scopes
            Log::info('Microsoft Admin Consent - Redirecting to OAuth flow for fresh connection');
            return redirect()->route('oauth.microsoft')
                ->with('success', 'Admin consent granted! Now connecting your calendar with approved permissions...');
        }
        
        return redirect()->route('connections.index')
            ->with('warning', 'Admin consent status unclear. Please try connecting your calendar again.');
    }
    
    /**
     * Show admin instructions page
     */
    public function showInstructions($connectionId)
    {
        $connection = CalendarConnection::findOrFail($connectionId);
        
        // Authorization check
        if ($connection->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Build admin consent URL for display
        $clientId = config('services.microsoft.client_id');
        $tenant = 'organizations'; // For work/school accounts
        $redirectUri = $this->getCurrentDomainUrl('/admin-consent/microsoft/callback');
        $scopes = implode(' ', config('services.microsoft.scopes'));
        
        $adminConsentUrl = sprintf(
            'https://login.microsoftonline.com/%s/v2.0/adminconsent?client_id=%s&redirect_uri=%s&scope=%s',
            $tenant,
            $clientId,
            urlencode($redirectUri),
            urlencode($scopes)
        );
        
        return view('connections.admin-instructions', compact('connection', 'adminConsentUrl'));
    }
    
    /**
     * Get URL with current domain (multi-domain support)
     */
    private function getCurrentDomainUrl(string $path): string
    {
        $appUrl = rtrim(config('app.url'), '/');
        $currentUrl = rtrim(url('/'), '/');
        
        return $currentUrl . $path;
    }
}

