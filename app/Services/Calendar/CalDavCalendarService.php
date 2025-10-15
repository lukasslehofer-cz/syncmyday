<?php

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use App\Services\Encryption\TokenEncryptionService;
use Illuminate\Support\Facades\Log;
use Sabre\DAV\Client;
use Sabre\VObject;

/**
 * CalDAV Calendar Service
 * 
 * Provides integration with CalDAV servers (Apple iCloud, Nextcloud, etc.)
 * Implements the same interface as Google/Microsoft services for consistency.
 */
class CalDavCalendarService
{
    private ?Client $client = null;
    private ?CalendarConnection $connection = null;
    private TokenEncryptionService $encryptionService;
    
    public function __construct(TokenEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }
    
    /**
     * Initialize service with a calendar connection
     */
    public function initializeWithConnection(CalendarConnection $connection): void
    {
        $this->connection = $connection;
        
        if ($connection->provider !== 'caldav') {
            throw new \Exception('Connection is not a CalDAV connection');
        }
        
        // Decrypt password
        $password = $this->encryptionService->decrypt($connection->caldav_password_encrypted);
        
        // Create Sabre DAV client
        $this->client = new Client([
            'baseUri' => $connection->caldav_url,
            'userName' => $connection->caldav_username,
            'password' => $password,
        ]);
        
        // Fix SSL certificate issues on shared hosting
        $this->client->addCurlSetting(CURLOPT_SSL_VERIFYPEER, false);
        $this->client->addCurlSetting(CURLOPT_SSL_VERIFYHOST, false);
        
        Log::channel('sync')->debug('CalDAV client initialized', [
            'connection_id' => $connection->id,
            'url' => $connection->caldav_url,
            'username' => $connection->caldav_username,
        ]);
    }
    
    /**
     * Automatic discovery for Apple iCloud
     * User only provides Apple ID + app-specific password
     */
    public static function discoverICloud(
        string $appleId,
        string $appPassword
    ): array {
        try {
            // iCloud CalDAV base URL
            $baseUrl = 'https://caldav.icloud.com';
            
            $client = new Client([
                'baseUri' => $baseUrl,
                'userName' => $appleId,
                'password' => $appPassword,
            ]);
            
            // Fix SSL certificate issues on shared hosting
            $client->addCurlSetting(CURLOPT_SSL_VERIFYPEER, false);
            $client->addCurlSetting(CURLOPT_SSL_VERIFYHOST, false);
            
            Log::info('iCloud discovery: Step 1 - Finding principal', [
                'apple_id' => $appleId,
            ]);
            
            // Try .well-known/caldav first for discovery
            $principalUrl = null;
            
            try {
                $response = $client->propfind('/.well-known/caldav', [
                    '{DAV:}current-user-principal',
                ], 0);
                
                if (isset($response['{DAV:}current-user-principal'])) {
                    $principal = $response['{DAV:}current-user-principal'];
                    
                    Log::debug('iCloud discovery: Principal raw response', [
                        'type' => gettype($principal),
                        'value' => is_array($principal) ? json_encode($principal) : (string)$principal,
                    ]);
                    
                    // Extract URL from response
                    $principalUrl = self::extractUrlFromPropfindResponse($principal);
                    
                    Log::info('iCloud discovery: Found principal via .well-known', [
                        'principal_url' => $principalUrl,
                    ]);
                }
            } catch (\Sabre\HTTP\ClientHttpException $e) {
                // Catch HTTP errors (401, 403, etc.)
                $statusCode = $e->getHttpStatus();
                
                Log::error('iCloud discovery: HTTP error during authentication', [
                    'status_code' => $statusCode,
                    'error' => $e->getMessage(),
                    'apple_id' => $appleId,
                ]);
                
                if ($statusCode === 401 || $statusCode === 403) {
                    throw new \Exception('Authentication failed. Please check your Apple ID and App-Specific Password. Make sure you generated a new App-Specific Password at appleid.apple.com (do not use your regular Apple password).');
                }
                
                throw new \Exception('HTTP error ' . $statusCode . ': ' . $e->getMessage());
            } catch (\Exception $e) {
                // Check if it's a connection error
                if (str_contains($e->getMessage(), 'Could not connect') || 
                    str_contains($e->getMessage(), 'Connection refused') ||
                    str_contains($e->getMessage(), 'Failed to connect')) {
                    Log::error('iCloud discovery: Connection error', [
                        'error' => $e->getMessage(),
                    ]);
                    throw new \Exception('Cannot connect to iCloud servers. Please check your internet connection.');
                }
                
                Log::warning('iCloud discovery: .well-known failed, trying root', [
                    'error' => $e->getMessage(),
                ]);
            }
            
            // If .well-known didn't work, try root
            if (!$principalUrl) {
                try {
                    $response = $client->propfind('/', [
                        '{DAV:}current-user-principal',
                    ], 0);
                    
                    if (!isset($response['{DAV:}current-user-principal'])) {
                        throw new \Exception('Could not discover CalDAV principal for iCloud');
                    }
                    
                    $principal = $response['{DAV:}current-user-principal'];
                    
                    Log::debug('iCloud discovery: Principal raw response (root)', [
                        'type' => gettype($principal),
                        'value' => is_array($principal) ? json_encode($principal) : (string)$principal,
                    ]);
                    
                    // Extract URL from response
                    $principalUrl = self::extractUrlFromPropfindResponse($principal);
                    
                    Log::info('iCloud discovery: Found principal via root', [
                        'principal_url' => $principalUrl,
                    ]);
                } catch (\Sabre\HTTP\ClientHttpException $e) {
                    $statusCode = $e->getHttpStatus();
                    
                    Log::error('iCloud discovery: HTTP error during root principal discovery', [
                        'status_code' => $statusCode,
                        'error' => $e->getMessage(),
                    ]);
                    
                    if ($statusCode === 401 || $statusCode === 403) {
                        throw new \Exception('Authentication failed. Please verify: 1) Your Apple ID email is correct, 2) You are using an App-Specific Password (not your regular password), generated at appleid.apple.com.');
                    }
                    
                    throw new \Exception('HTTP error ' . $statusCode . ' while connecting to iCloud: ' . $e->getMessage());
                }
            }
            
            // Get calendar home set
            Log::info('iCloud discovery: Step 2 - Finding calendar home', [
                'principal_url' => $principalUrl,
            ]);
            
            try {
                $response = $client->propfind($principalUrl, [
                    '{urn:ietf:params:xml:ns:caldav}calendar-home-set',
                ], 0);
                
                if (!isset($response['{urn:ietf:params:xml:ns:caldav}calendar-home-set'])) {
                    throw new \Exception('Could not discover calendar home for iCloud. Your account may not have CalDAV access enabled.');
                }
                
                $calendarHome = $response['{urn:ietf:params:xml:ns:caldav}calendar-home-set'];
                
                Log::debug('iCloud discovery: Calendar home raw response', [
                    'type' => gettype($calendarHome),
                    'value' => is_array($calendarHome) ? json_encode($calendarHome) : (string)$calendarHome,
                ]);
                
                // Extract URL from response
                $calendarHomeUrl = self::extractUrlFromPropfindResponse($calendarHome);
                
                Log::info('iCloud discovery: Step 3 - Listing calendars', [
                    'calendar_home_url' => $calendarHomeUrl,
                ]);
                
                // List calendars
                $calendars = self::listCalendarsFromUrl($client, $calendarHomeUrl);
                
                Log::info('iCloud discovery: Success!', [
                    'calendars_found' => count($calendars),
                ]);
            } catch (\Sabre\HTTP\ClientHttpException $e) {
                $statusCode = $e->getHttpStatus();
                
                Log::error('iCloud discovery: HTTP error during calendar discovery', [
                    'status_code' => $statusCode,
                    'error' => $e->getMessage(),
                ]);
                
                throw new \Exception('Failed to access iCloud calendars (HTTP ' . $statusCode . '). Please check your credentials.');
            }
            
            return [
                'success' => true,
                'url' => $baseUrl,
                'principal_url' => $principalUrl,
                'calendar_home_url' => $calendarHomeUrl,
                'calendars' => $calendars,
            ];
            
        } catch (\Exception $e) {
            Log::error('iCloud discovery failed', [
                'apple_id' => $appleId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Test connection and discover CalDAV server
     * Returns principal URL and available calendars
     */
    public static function testConnection(
        string $url,
        string $username,
        string $password
    ): array {
        try {
            $client = new Client([
                'baseUri' => $url,
                'userName' => $username,
                'password' => $password,
            ]);
            
            // Fix SSL certificate issues on shared hosting
            $client->addCurlSetting(CURLOPT_SSL_VERIFYPEER, false);
            $client->addCurlSetting(CURLOPT_SSL_VERIFYHOST, false);
            
            // Try to discover principal URL
            $response = $client->propfind('', [
                '{DAV:}current-user-principal',
            ], 0);
            
            if (!isset($response['{DAV:}current-user-principal'])) {
                throw new \Exception('Could not discover CalDAV principal');
            }
            
            $principalUrl = self::extractUrlFromPropfindResponse($response['{DAV:}current-user-principal']);
            
            // Get calendar home set
            $response = $client->propfind($principalUrl, [
                '{urn:ietf:params:xml:ns:caldav}calendar-home-set',
            ], 0);
            
            if (!isset($response['{urn:ietf:params:xml:ns:caldav}calendar-home-set'])) {
                throw new \Exception('Could not discover calendar home');
            }
            
            $calendarHomeUrl = self::extractUrlFromPropfindResponse($response['{urn:ietf:params:xml:ns:caldav}calendar-home-set']);
            
            // List calendars
            $calendars = self::listCalendarsFromUrl($client, $calendarHomeUrl);
            
            return [
                'success' => true,
                'principal_url' => $principalUrl,
                'calendar_home_url' => $calendarHomeUrl,
                'calendars' => $calendars,
            ];
            
        } catch (\Exception $e) {
            Log::error('CalDAV connection test failed', [
                'url' => $url,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * List calendars from a calendar home URL
     */
    private static function listCalendarsFromUrl(Client $client, string $calendarHomeUrl): array
    {
        $response = $client->propfind($calendarHomeUrl, [
            '{DAV:}resourcetype',
            '{DAV:}displayname',
            '{http://apple.com/ns/ical/}calendar-color',
            '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set',
        ], 1);
        
        Log::info('CalDAV: Found resources in calendar home', [
            'calendar_home_url' => $calendarHomeUrl,
            'resource_count' => count($response),
        ]);
        
        $calendars = [];
        
        foreach ($response as $url => $props) {
            Log::debug('CalDAV: Processing resource', [
                'url' => $url,
                'has_resourcetype' => isset($props['{DAV:}resourcetype']),
                'props_keys' => array_keys($props),
            ]);
            
            // Check if this is a calendar
            if (!isset($props['{DAV:}resourcetype'])) {
                Log::debug('CalDAV: Skipping - no resourcetype', ['url' => $url]);
                continue;
            }
            
            // Get resourceType value (could be object or already an array)
            $resourceTypeProp = $props['{DAV:}resourcetype'];
            if (is_object($resourceTypeProp) && method_exists($resourceTypeProp, 'getValue')) {
                $resourceType = $resourceTypeProp->getValue();
            } elseif (is_array($resourceTypeProp)) {
                $resourceType = $resourceTypeProp;
            } else {
                $resourceType = [$resourceTypeProp];
            }
            
            $isCalendar = false;
            
            // Normalize resourceType to array
            if (!is_array($resourceType)) {
                Log::debug('CalDAV: resourceType is not array', [
                    'url' => $url,
                    'type' => gettype($resourceType),
                    'value' => $resourceType,
                ]);
                $resourceType = [$resourceType];
            }
            
            Log::debug('CalDAV: Checking resourceType', [
                'url' => $url,
                'resourceType_count' => count($resourceType),
                'resourceType_dump' => print_r($resourceType, true),
            ]);
            
            foreach ($resourceType as $type) {
                // Handle string format: {namespace}localname
                if (is_string($type)) {
                    Log::debug('CalDAV: resourceType element is string', [
                        'url' => $url,
                        'type' => $type,
                    ]);
                    
                    // Check if it's a calendar type
                    if ($type === '{urn:ietf:params:xml:ns:caldav}calendar') {
                        $isCalendar = true;
                        break;
                    }
                }
                // Handle array with name/namespaceURI keys
                elseif (is_array($type)) {
                    Log::debug('CalDAV: resourceType element is array', [
                        'url' => $url,
                        'type' => $type,
                    ]);
                    
                    if (isset($type['name']) && $type['name'] === 'calendar' && 
                        isset($type['namespaceURI']) && $type['namespaceURI'] === 'urn:ietf:params:xml:ns:caldav') {
                        $isCalendar = true;
                        break;
                    }
                } 
                // Handle object
                elseif (is_object($type)) {
                    $name = property_exists($type, 'name') ? $type->name : null;
                    $namespace = property_exists($type, 'namespaceURI') ? $type->namespaceURI : null;
                    
                    Log::debug('CalDAV: resourceType element is object', [
                        'url' => $url,
                        'class' => get_class($type),
                        'name' => $name,
                        'namespace' => $namespace,
                        'object_dump' => print_r($type, true),
                    ]);
                    
                    if ($name === 'calendar' && $namespace === 'urn:ietf:params:xml:ns:caldav') {
                        $isCalendar = true;
                        break;
                    }
                }
            }
            
            if (!$isCalendar) {
                Log::info('CalDAV: Skipping - not a calendar', [
                    'url' => $url,
                    'resourceType_count' => count($resourceType),
                ]);
                continue;
            }
            
            Log::info('CalDAV: Found calendar resource!', ['url' => $url]);
            
            // Check if calendar supports VEVENT
            $supportsEvents = false;
            if (isset($props['{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set'])) {
                // Get components value (could be object or already an array)
                $componentsProp = $props['{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set'];
                if (is_object($componentsProp) && method_exists($componentsProp, 'getValue')) {
                    $components = $componentsProp->getValue();
                } elseif (is_array($componentsProp)) {
                    $components = $componentsProp;
                } else {
                    $components = [$componentsProp];
                }
                
                // Normalize components to array
                if (!is_array($components)) {
                    Log::debug('CalDAV: components is not array', [
                        'url' => $url,
                        'type' => gettype($components),
                        'value' => $components,
                    ]);
                    $components = [$components];
                }
                
                Log::debug('CalDAV: Checking supported components', [
                    'url' => $url,
                    'components_count' => count($components),
                    'components_dump' => print_r($components, true),
                ]);
                
                foreach ($components as $component) {
                    // Handle string format: {namespace}localname or just localname
                    if (is_string($component)) {
                        Log::debug('CalDAV: Component is string', [
                            'url' => $url,
                            'component' => $component,
                        ]);
                        
                        // Check if it contains VEVENT (with or without namespace)
                        if ($component === 'VEVENT' || strpos($component, 'VEVENT') !== false) {
                            $supportsEvents = true;
                            break;
                        }
                    }
                    // Handle array with name key or attributes
                    elseif (is_array($component)) {
                        Log::debug('CalDAV: Component is array', [
                            'url' => $url,
                            'component' => $component,
                        ]);
                        
                        // Check direct name
                        if (isset($component['name']) && $component['name'] === 'VEVENT') {
                            $supportsEvents = true;
                            break;
                        }
                        
                        // Check attributes.name (iCloud format)
                        if (isset($component['attributes']['name']) && $component['attributes']['name'] === 'VEVENT') {
                            $supportsEvents = true;
                            Log::info('CalDAV: Found VEVENT in attributes!', ['url' => $url]);
                            break;
                        }
                        
                        // Check if it's a simple associative array with 'VEVENT' value
                        if (in_array('VEVENT', $component, true)) {
                            $supportsEvents = true;
                            break;
                        }
                    } 
                    // Handle object
                    elseif (is_object($component)) {
                        $name = property_exists($component, 'name') ? $component->name : null;
                        
                        Log::debug('CalDAV: Component is object', [
                            'url' => $url,
                            'class' => get_class($component),
                            'name' => $name,
                            'object_dump' => print_r($component, true),
                        ]);
                        
                        if ($name === 'VEVENT') {
                            $supportsEvents = true;
                            break;
                        }
                    }
                }
            } else {
                Log::debug('CalDAV: No supported-calendar-component-set property', ['url' => $url]);
            }
            
            if (!$supportsEvents) {
                Log::info('CalDAV: Skipping calendar - does not support VEVENT', ['url' => $url]);
                continue;
            }
            
            Log::info('CalDAV: Calendar supports VEVENT!', ['url' => $url]);
            
            // Get display name
            if (isset($props['{DAV:}displayname'])) {
                $displayNameProp = $props['{DAV:}displayname'];
                if (is_object($displayNameProp) && method_exists($displayNameProp, 'getValue')) {
                    $displayName = $displayNameProp->getValue();
                } else {
                    $displayName = is_string($displayNameProp) ? $displayNameProp : basename($url);
                }
            } else {
                $displayName = basename($url);
            }
            
            // Get calendar color
            if (isset($props['{http://apple.com/ns/ical/}calendar-color'])) {
                $colorProp = $props['{http://apple.com/ns/ical/}calendar-color'];
                if (is_object($colorProp) && method_exists($colorProp, 'getValue')) {
                    $color = $colorProp->getValue();
                } else {
                    $color = is_string($colorProp) ? $colorProp : null;
                }
            } else {
                $color = null;
            }
            
            $calendars[] = [
                'id' => $url,
                'name' => $displayName,
                'color' => $color,
            ];
        }
        
        return $calendars;
    }
    
    /**
     * Get list of available calendars for this connection
     */
    public function getCalendars(): array
    {
        if (!$this->client || !$this->connection) {
            throw new \Exception('Service not initialized');
        }
        
        $calendarHomeUrl = $this->connection->caldav_principal_url;
        
        if (!$calendarHomeUrl) {
            throw new \Exception('Calendar home URL not set');
        }
        
        return self::listCalendarsFromUrl($this->client, $calendarHomeUrl);
    }
    
    /**
     * Get account information
     */
    public function getAccountInfo(): array
    {
        if (!$this->connection) {
            throw new \Exception('Service not initialized');
        }
        
        return [
            'id' => $this->connection->caldav_username,
            'email' => $this->connection->account_email ?? $this->connection->caldav_username,
        ];
    }
    
    /**
     * Create a busy blocker event in CalDAV calendar
     */
    public function createBlocker(
        string $calendarId,
        string $title,
        \DateTime $start,
        \DateTime $end,
        string $transactionId
    ): string {
        if (!$this->client) {
            throw new \Exception('Service not initialized');
        }
        
        // Generate unique event ID
        $eventUid = \Str::uuid()->toString();
        $eventUrl = rtrim($calendarId, '/') . '/' . $eventUid . '.ics';
        
        // Create VEVENT using Sabre VObject
        $vcalendar = new VObject\Component\VCalendar();
        $vevent = $vcalendar->add('VEVENT', [
            'SUMMARY' => $title,
            'DTSTART' => $start,
            'DTEND' => $end,
            'UID' => $eventUid,
            'DTSTAMP' => new \DateTime(),
            'STATUS' => 'CONFIRMED',
            'TRANSP' => 'OPAQUE', // Shows as busy
            'CLASS' => 'PRIVATE',
            'DESCRIPTION' => 'Auto-synced by SyncMyDay',
        ]);
        
        // Add custom property to mark as our blocker
        $vevent->add('X-SYNCMYDAY-BLOCKER', 'true');
        $vevent->add('X-SYNCMYDAY-TRANSACTION-ID', $transactionId);
        
        // Convert to iCalendar format
        $icsContent = $vcalendar->serialize();
        
        // PUT to CalDAV server
        $this->client->request('PUT', $eventUrl, $icsContent, [
            'Content-Type' => 'text/calendar; charset=utf-8',
        ]);
        
        Log::channel('sync')->info('CalDAV blocker created', [
            'calendar_id' => $calendarId,
            'event_uid' => $eventUid,
            'event_url' => $eventUrl,
            'transaction_id' => $transactionId,
        ]);
        
        return $eventUid;
    }
    
    /**
     * Update a blocker event
     */
    public function updateBlocker(
        string $calendarId,
        string $eventId,
        string $title,
        \DateTime $start,
        \DateTime $end,
        string $transactionId
    ): void {
        if (!$this->client) {
            throw new \Exception('Service not initialized');
        }
        
        $eventUrl = rtrim($calendarId, '/') . '/' . $eventId . '.ics';
        
        // Get existing event
        $response = $this->client->request('GET', $eventUrl);
        $vcalendar = VObject\Reader::read($response['body']);
        $vevent = $vcalendar->VEVENT;
        
        // Update properties
        $vevent->SUMMARY = $title;
        $vevent->DTSTART = $start;
        $vevent->DTEND = $end;
        $vevent->{'LAST-MODIFIED'} = new \DateTime();
        
        // Increment sequence
        if (isset($vevent->SEQUENCE)) {
            $currentSequence = $vevent->SEQUENCE;
            $sequenceValue = is_object($currentSequence) && method_exists($currentSequence, 'getValue') 
                ? $currentSequence->getValue() 
                : (int) $currentSequence;
            $vevent->SEQUENCE = $sequenceValue + 1;
        } else {
            $vevent->add('SEQUENCE', 1);
        }
        
        // Convert to iCalendar format
        $icsContent = $vcalendar->serialize();
        
        // PUT updated event
        $this->client->request('PUT', $eventUrl, $icsContent, [
            'Content-Type' => 'text/calendar; charset=utf-8',
        ]);
        
        Log::channel('sync')->info('CalDAV blocker updated', [
            'calendar_id' => $calendarId,
            'event_id' => $eventId,
            'transaction_id' => $transactionId,
        ]);
    }
    
    /**
     * Delete a blocker event
     */
    public function deleteBlocker(string $calendarId, string $eventId): void
    {
        if (!$this->client) {
            throw new \Exception('Service not initialized');
        }
        
        $eventUrl = rtrim($calendarId, '/') . '/' . $eventId . '.ics';
        
        // DELETE from CalDAV server
        $this->client->request('DELETE', $eventUrl);
        
        Log::channel('sync')->info('CalDAV blocker deleted', [
            'calendar_id' => $calendarId,
            'event_id' => $eventId,
        ]);
    }
    
    /**
     * Check if event is a SyncMyDay blocker
     */
    public function isOurBlocker($event): bool
    {
        if (is_array($event)) {
            $hasBlockerLower = isset($event['x-syncmyday-blocker']) && $event['x-syncmyday-blocker'];
            $hasBlockerUpper = isset($event['X-SYNCMYDAY-BLOCKER']) && $event['X-SYNCMYDAY-BLOCKER'];
            return $hasBlockerLower || $hasBlockerUpper;
        }
        
        // VObject event
        return isset($event->{'X-SYNCMYDAY-BLOCKER'});
    }
    
    /**
     * Get events changed since last sync (polling-based)
     * Returns events with their metadata
     */
    public function getChangedEvents(string $calendarId, ?string $syncToken = null): array
    {
        if (!$this->client) {
            throw new \Exception('Service not initialized');
        }
        
        // For initial sync or if no sync token, get all events in time range
        if (!$syncToken) {
            return $this->getEventsInRange($calendarId);
        }
        
        // Use sync-collection for incremental sync (if supported)
        try {
            return $this->getSyncCollection($calendarId, $syncToken);
        } catch (\Exception $e) {
            Log::channel('sync')->warning('sync-collection not supported, falling back to full sync', [
                'calendar_id' => $calendarId,
                'error' => $e->getMessage(),
            ]);
            
            return $this->getEventsInRange($calendarId);
        }
    }
    
    /**
     * Get all events in a time range (for initial sync)
     */
    private function getEventsInRange(string $calendarId): array
    {
        $pastDays = config('sync.time_range.past_days', 7);
        $futureMonths = config('sync.time_range.future_months', 6);
        
        $start = now()->subDays($pastDays)->format('Ymd\THis\Z');
        $end = now()->addMonths($futureMonths)->format('Ymd\THis\Z');
        
        // CalDAV calendar-query
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<C:calendar-query xmlns:D="DAV:" xmlns:C="urn:ietf:params:xml:ns:caldav">
    <D:prop>
        <D:getetag/>
        <C:calendar-data/>
    </D:prop>
    <C:filter>
        <C:comp-filter name="VCALENDAR">
            <C:comp-filter name="VEVENT">
                <C:time-range start="{$start}" end="{$end}"/>
            </C:comp-filter>
        </C:comp-filter>
    </C:filter>
</C:calendar-query>
XML;
        
        $response = $this->client->request('REPORT', $calendarId, $xml, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Depth' => '1',
        ]);
        
        $events = $this->parseMultistatusResponse($response['body']);
        
        Log::channel('sync')->info('CalDAV: Parsed events', [
            'calendar_id' => $calendarId,
            'event_count' => count($events),
        ]);
        
        // Get new sync token (ctag)
        $newSyncToken = $this->getCalendarCtag($calendarId);
        
        return [
            'events' => $events,
            'sync_token' => $newSyncToken,
        ];
    }
    
    /**
     * Get calendar ctag (change tag) for sync tracking
     */
    private function getCalendarCtag(string $calendarId): string
    {
        $response = $this->client->propfind($calendarId, [
            '{http://calendarserver.org/ns/}getctag',
        ], 0);
        
        if (isset($response['{http://calendarserver.org/ns/}getctag'])) {
            $ctag = $response['{http://calendarserver.org/ns/}getctag'];
            
            // Handle different response types
            if (is_string($ctag)) {
                return $ctag;
            } elseif (is_object($ctag) && method_exists($ctag, 'getValue')) {
                return $ctag->getValue();
            }
        }
        
        // Fallback to current timestamp
        return (string) time();
    }
    
    /**
     * Get sync-collection (incremental changes)
     */
    private function getSyncCollection(string $calendarId, string $syncToken): array
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<D:sync-collection xmlns:D="DAV:">
    <D:sync-token>{$syncToken}</D:sync-token>
    <D:sync-level>1</D:sync-level>
    <D:prop>
        <D:getetag/>
    </D:prop>
</D:sync-collection>
XML;
        
        $response = $this->client->request('REPORT', $calendarId, $xml, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
        
        // Parse response and fetch full calendar data for changed events
        $changedUrls = $this->parseSyncCollectionResponse($response['body']);
        $events = [];
        
        foreach ($changedUrls as $eventUrl) {
            try {
                $eventResponse = $this->client->request('GET', $eventUrl);
                $vcalendar = VObject\Reader::read($eventResponse['body']);
                
                if (isset($vcalendar->VEVENT)) {
                    $events[] = $this->parseVEvent($vcalendar->VEVENT, $eventUrl);
                }
            } catch (\Exception $e) {
                Log::channel('sync')->warning('Failed to fetch changed event', [
                    'url' => $eventUrl,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        // Get new sync token
        $newSyncToken = $this->extractSyncToken($response['body']);
        
        return [
            'events' => $events,
            'sync_token' => $newSyncToken ?? $this->getCalendarCtag($calendarId),
        ];
    }
    
    /**
     * Parse multistatus response from calendar-query
     */
    private function parseMultistatusResponse(string $xml): array
    {
        $reader = new \Sabre\Xml\Reader();
        $reader->xml($xml);
        $result = $reader->parse();
        
        $events = [];
        
        // Find all response elements
        if (isset($result['value']) && is_array($result['value'])) {
            foreach ($result['value'] as $index => $response) {
                if (!isset($response['value'])) {
                    continue;
                }
                
                $href = null;
                $calendarData = null;
                
                foreach ($response['value'] as $prop) {
                    if ($prop['name'] === '{DAV:}href') {
                        $href = $prop['value'];
                    } elseif ($prop['name'] === '{DAV:}propstat' && isset($prop['value']) && is_array($prop['value'])) {
                        // propstat contains: {DAV:}prop and {DAV:}status
                        foreach ($prop['value'] as $propstatChild) {
                            if (isset($propstatChild['name']) && $propstatChild['name'] === '{DAV:}prop') {
                                // Now we're inside <prop>, look for <calendar-data>
                                if (isset($propstatChild['value']) && is_array($propstatChild['value'])) {
                                    foreach ($propstatChild['value'] as $propChild) {
                                        if (isset($propChild['name']) && $propChild['name'] === '{urn:ietf:params:xml:ns:caldav}calendar-data') {
                                            $calendarData = $propChild['value'];
                                            break 3; // Break out of all loops
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ($calendarData) {
                    try {
                        $vcalendar = VObject\Reader::read($calendarData);
                        
                        if (isset($vcalendar->VEVENT)) {
                            $events[] = $this->parseVEvent($vcalendar->VEVENT, $href);
                        }
                    } catch (\Exception $e) {
                        Log::channel('sync')->warning('Failed to parse calendar data', [
                            'href' => $href,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }
        
        return $events;
    }
    
    /**
     * Parse VEVENT component to array
     */
    private function parseVEvent($vevent, ?string $href = null): array
    {
        $uid = (string) $vevent->UID;
        $summary = isset($vevent->SUMMARY) ? (string) $vevent->SUMMARY : 'Untitled';
        $description = isset($vevent->DESCRIPTION) ? (string) $vevent->DESCRIPTION : null;
        $location = isset($vevent->LOCATION) ? (string) $vevent->LOCATION : null;
        $status = isset($vevent->STATUS) ? strtolower((string) $vevent->STATUS) : 'confirmed';
        
        // Parse dates (convert DateTimeImmutable to DateTime)
        $dtstartImmutable = $vevent->DTSTART->getDateTime();
        $dtstart = \DateTime::createFromImmutable($dtstartImmutable);
        
        if (isset($vevent->DTEND)) {
            $dtendImmutable = $vevent->DTEND->getDateTime();
            $dtend = \DateTime::createFromImmutable($dtendImmutable);
        } else {
            $dtend = clone $dtstart;
        }
        
        // Check if all-day
        $isAllDay = !$vevent->DTSTART->hasTime();
        
        // Get transparency (TRANSP)
        $transp = isset($vevent->TRANSP) ? strtolower((string) $vevent->TRANSP) : 'opaque';
        $showAs = $transp === 'transparent' ? 'free' : 'busy';
        
        // Check for X-SYNCMYDAY-BLOCKER
        $isSyncMyDayBlocker = isset($vevent->{'X-SYNCMYDAY-BLOCKER'});
        
        return [
            'id' => $uid,
            'uid' => $uid,
            'href' => $href,
            'summary' => $summary,
            'description' => $description,
            'location' => $location,
            'start' => $dtstart,
            'end' => $dtend,
            'isAllDay' => $isAllDay,
            'status' => $status,
            'showAs' => $showAs,
            'busyStatus' => $showAs,
            'x-syncmyday-blocker' => $isSyncMyDayBlocker,
        ];
    }
    
    /**
     * Parse sync-collection response
     */
    private function parseSyncCollectionResponse(string $xml): array
    {
        // Simple parsing - extract hrefs of changed resources
        $urls = [];
        
        if (preg_match_all('/<D:href>([^<]+)<\/D:href>/', $xml, $matches)) {
            $urls = $matches[1];
        }
        
        return $urls;
    }
    
    /**
     * Extract sync-token from response
     */
    private function extractSyncToken(string $xml): ?string
    {
        if (preg_match('/<D:sync-token>([^<]+)<\/D:sync-token>/', $xml, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Extract URL string from PROPFIND response (handles various return types)
     */
    private static function extractUrlFromPropfindResponse($response): string
    {
        // Direct string
        if (is_string($response)) {
            return $response;
        }
        
        // Object with getHref() method (Sabre\DAV\Xml\Element\Href)
        if (is_object($response) && method_exists($response, 'getHref')) {
            return $response->getHref();
        }
        
        // Array - recursively extract
        if (is_array($response)) {
            // Try common array structures
            if (isset($response['href'])) {
                return self::extractUrlFromPropfindResponse($response['href']);
            }
            
            if (isset($response[0])) {
                return self::extractUrlFromPropfindResponse($response[0]);
            }
            
            // If array has 'value' key
            if (isset($response['value'])) {
                return self::extractUrlFromPropfindResponse($response['value']);
            }
            
            // Last resort: take first non-empty value
            foreach ($response as $value) {
                if (!empty($value)) {
                    return self::extractUrlFromPropfindResponse($value);
                }
            }
        }
        
        // Last resort: convert to string
        if (is_object($response) && method_exists($response, '__toString')) {
            return (string) $response;
        }
        
        throw new \Exception('Unable to extract URL from PROPFIND response: ' . print_r($response, true));
    }
}

