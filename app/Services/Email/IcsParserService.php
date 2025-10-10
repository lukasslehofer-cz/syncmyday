<?php

namespace App\Services\Email;

use Sabre\VObject\Reader;
use Illuminate\Support\Facades\Log;

/**
 * ICS Parser Service
 * 
 * Parses iCalendar (.ics) files and extracts event data
 */
class IcsParserService
{
    /**
     * Parse .ics file content and return array of events
     *
     * @param string $icsContent Raw .ics file content
     * @return array Array of event data
     */
    public function parseIcsFile(string $icsContent): array
    {
        try {
            // Clean up content (remove BOM, fix line endings)
            $icsContent = trim($icsContent);
            $icsContent = str_replace(["\r\n", "\r"], "\n", $icsContent);
            
            $vcalendar = Reader::read($icsContent);
            $events = [];

            if (!isset($vcalendar->VEVENT)) {
                Log::warning('No VEVENT found in .ics file', [
                    'has_vcalendar' => isset($vcalendar),
                    'components' => $vcalendar ? array_keys((array) $vcalendar->children()) : [],
                ]);
                return [];
            }

            $eventCount = is_array($vcalendar->VEVENT) ? count($vcalendar->VEVENT) : 1;
            Log::info("Found {$eventCount} VEVENT(s) in .ics file");

            foreach ($vcalendar->VEVENT as $vevent) {
                $eventData = $this->extractEventData($vevent);
                
                if ($eventData) {
                    $events[] = $eventData;
                    Log::info('Successfully extracted event', [
                        'uid' => $eventData['uid'],
                        'start' => $eventData['start']->format('Y-m-d H:i:s'),
                        'summary' => $eventData['summary'],
                    ]);
                } else {
                    Log::warning('Failed to extract event data', [
                        'uid' => isset($vevent->UID) ? (string) $vevent->UID : 'no UID',
                    ]);
                }
            }

            Log::info('Parsed .ics file', [
                'events_found' => $eventCount,
                'events_extracted' => count($events),
            ]);

            return $events;

        } catch (\Exception $e) {
            Log::error('Failed to parse .ics file', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'content_length' => strlen($icsContent),
                'content_preview' => substr($icsContent, 0, 200),
            ]);
            
            throw new \Exception('Invalid .ics file format: ' . $e->getMessage());
        }
    }

    /**
     * Extract event data from VEVENT component
     */
    private function extractEventData($vevent): ?array
    {
        try {
            // Required fields
            if (!isset($vevent->UID) || !isset($vevent->DTSTART)) {
                Log::warning('Event missing required fields', [
                    'has_uid' => isset($vevent->UID),
                    'has_dtstart' => isset($vevent->DTSTART),
                    'properties' => array_keys((array) $vevent->children()),
                ]);
                return null;
            }

            $uid = (string) $vevent->UID;
            
            Log::info('Parsing event datetime', [
                'uid' => $uid,
                'dtstart_raw' => (string) $vevent->DTSTART,
            ]);
            
            $start = $this->parseDateTime($vevent->DTSTART);
            
            // If DTEND is not set, use DURATION or default to start time
            if (isset($vevent->DTEND)) {
                $end = $this->parseDateTime($vevent->DTEND);
            } elseif (isset($vevent->DURATION)) {
                $end = clone $start;
                $end->add($vevent->DURATION->getDateInterval());
            } else {
                // Default: 1 hour event
                $end = clone $start;
                $end->modify('+1 hour');
            }

            return [
                'uid' => $uid,
                'start' => $start,
                'end' => $end,
                'summary' => isset($vevent->SUMMARY) ? (string) $vevent->SUMMARY->getValue() : 'Busy',
                'description' => isset($vevent->DESCRIPTION) ? (string) $vevent->DESCRIPTION->getValue() : '',
                'location' => isset($vevent->LOCATION) ? (string) $vevent->LOCATION->getValue() : '',
                'status' => isset($vevent->STATUS) ? (string) $vevent->STATUS->getValue() : 'CONFIRMED',
                'sequence' => isset($vevent->SEQUENCE) ? (int) $vevent->SEQUENCE->getValue() : 0,
                'method' => $this->extractMethod($vevent),
                'organizer' => $this->extractOrganizer($vevent),
                'is_all_day' => $this->isAllDayEvent($vevent->DTSTART),
            ];

        } catch (\Exception $e) {
            Log::error('Exception while extracting event data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'uid' => isset($vevent->UID) ? (string) $vevent->UID : 'unknown',
            ]);
            
            return null;
        }
    }

    /**
     * Parse date/time from iCalendar property
     */
    private function parseDateTime($dtProperty): \DateTime
    {
        try {
            $dt = $dtProperty->getDateTime();
            // Convert DateTimeImmutable to DateTime if needed
            if ($dt instanceof \DateTimeImmutable) {
                return \DateTime::createFromImmutable($dt);
            }
            return $dt;
        } catch (\Exception $e) {
            // Fallback: try to parse as string
            $dateString = (string) $dtProperty;
            return new \DateTime($dateString);
        }
    }

    /**
     * Extract METHOD from parent VCALENDAR or VEVENT
     */
    private function extractMethod($vevent): ?string
    {
        // METHOD is typically in VCALENDAR, not VEVENT
        $vcalendar = $vevent->parent;
        
        if ($vcalendar && isset($vcalendar->METHOD)) {
            return strtoupper((string) $vcalendar->METHOD);
        }
        
        return null;
    }

    /**
     * Extract organizer email
     */
    private function extractOrganizer($vevent): ?string
    {
        if (!isset($vevent->ORGANIZER)) {
            return null;
        }
        
        $organizer = (string) $vevent->ORGANIZER;
        
        // Remove "mailto:" prefix if present
        if (str_starts_with($organizer, 'mailto:')) {
            $organizer = substr($organizer, 7);
        }
        
        return $organizer;
    }

    /**
     * Check if event is all-day
     */
    private function isAllDayEvent($dtstart): bool
    {
        // All-day events use VALUE=DATE instead of VALUE=DATE-TIME
        return $dtstart->hasTime() === false;
    }

    /**
     * Detect if this is a cancellation
     */
    public function isCancellation(array $eventData): bool
    {
        return $eventData['method'] === 'CANCEL' || $eventData['status'] === 'CANCELLED';
    }

    /**
     * Detect if this is an update (SEQUENCE > 0)
     */
    public function isUpdate(array $eventData): bool
    {
        return $eventData['sequence'] > 0;
    }
}

