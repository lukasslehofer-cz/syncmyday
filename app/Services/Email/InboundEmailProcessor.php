<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Log;

/**
 * Inbound Email Processor
 * 
 * Processes incoming calendar responses (ACCEPT/DECLINE/TENTATIVE)
 * from email calendar systems via Mailgun webhooks
 */
class InboundEmailProcessor
{
    /**
     * Process calendar response (iMIP)
     * 
     * @param string $from Sender email address
     * @param string $to Recipient email (events@syncmyday.*)
     * @param string $icsContent .ics file content
     * @return array Result with 'success' boolean and optional 'error' message
     */
    public function processCalendarResponse(string $from, string $to, string $icsContent): array
    {
        try {
            // Parse .ics content
            $parsedData = $this->parseIcsContent($icsContent);
            
            if (!$parsedData) {
                return [
                    'success' => false,
                    'error' => 'Failed to parse .ics content',
                ];
            }

            // Extract event details
            $eventUid = $parsedData['uid'] ?? null;
            $method = $parsedData['method'] ?? 'UNKNOWN';
            $status = $parsedData['partstat'] ?? 'UNKNOWN';

            Log::info('Calendar response parsed', [
                'from' => $from,
                'to' => $to,
                'event_uid' => $eventUid,
                'method' => $method,
                'status' => $status,
            ]);

            // TODO: Update blocker status in database
            // For now, just log the response
            // In future: mark blocker as confirmed/declined in email_calendar_events table

            return [
                'success' => true,
                'event_uid' => $eventUid,
                'method' => $method,
                'status' => $status,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to process calendar response', [
                'error' => $e->getMessage(),
                'from' => $from,
                'to' => $to,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Parse .ics content and extract key fields
     * 
     * @param string $icsContent
     * @return array|null Parsed data or null on failure
     */
    private function parseIcsContent(string $icsContent): ?array
    {
        $lines = explode("\n", str_replace("\r\n", "\n", $icsContent));
        
        $data = [
            'method' => null,
            'uid' => null,
            'partstat' => null,
            'sequence' => 0,
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Parse METHOD
            if (str_starts_with($line, 'METHOD:')) {
                $data['method'] = trim(substr($line, 7));
            }
            
            // Parse UID
            if (str_starts_with($line, 'UID:')) {
                $data['uid'] = trim(substr($line, 4));
            }
            
            // Parse PARTSTAT (participant status)
            if (str_contains($line, 'PARTSTAT=')) {
                preg_match('/PARTSTAT=([A-Z-]+)/', $line, $matches);
                if (isset($matches[1])) {
                    $data['partstat'] = $matches[1];
                }
            }
            
            // Parse SEQUENCE
            if (str_starts_with($line, 'SEQUENCE:')) {
                $data['sequence'] = (int) trim(substr($line, 9));
            }
        }

        // Return null if essential fields missing
        if (!$data['uid']) {
            return null;
        }

        return $data;
    }
}


