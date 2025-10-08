<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Log;

/**
 * Email Parser Service
 * 
 * Parses raw email content and extracts .ics attachments
 */
class EmailParserService
{
    /**
     * Parse raw email and extract .ics attachments
     *
     * @param string $rawEmail Raw email content (headers + body)
     * @return array ['from' => string, 'subject' => string, 'ics_attachments' => array]
     */
    public function parseEmail(string $rawEmail): array
    {
        $result = [
            'from' => null,
            'subject' => null,
            'ics_attachments' => [],
        ];

        // Extract headers
        $headers = $this->extractHeaders($rawEmail);
        $result['from'] = $this->extractEmailAddress($headers['from'] ?? '');
        $result['subject'] = $headers['subject'] ?? 'No Subject';

        // Extract .ics attachments
        $result['ics_attachments'] = $this->extractIcsAttachments($rawEmail);

        Log::info('Parsed email', [
            'from' => $result['from'],
            'subject' => $result['subject'],
            'ics_count' => count($result['ics_attachments']),
        ]);

        return $result;
    }

    /**
     * Extract headers from raw email
     */
    private function extractHeaders(string $rawEmail): array
    {
        $headers = [];
        
        // Split headers and body
        $parts = preg_split("/\r?\n\r?\n/", $rawEmail, 2);
        
        if (count($parts) < 2) {
            return $headers;
        }
        
        $headerLines = explode("\n", $parts[0]);
        $currentHeader = null;
        
        foreach ($headerLines as $line) {
            $line = rtrim($line);
            
            // Continuation line (starts with space or tab)
            if ($currentHeader && (str_starts_with($line, ' ') || str_starts_with($line, "\t"))) {
                $headers[$currentHeader] .= ' ' . trim($line);
                continue;
            }
            
            // New header
            if (str_contains($line, ':')) {
                [$name, $value] = explode(':', $line, 2);
                $name = strtolower(trim($name));
                $value = trim($value);
                
                $headers[$name] = $value;
                $currentHeader = $name;
            }
        }
        
        return $headers;
    }

    /**
     * Extract email address from From header
     */
    private function extractEmailAddress(string $from): ?string
    {
        if (empty($from)) {
            return null;
        }
        
        // Try to extract email from "Name <email@domain.com>" format
        if (preg_match('/<([^>]+)>/', $from, $matches)) {
            return strtolower(trim($matches[1]));
        }
        
        // Fallback: assume the whole string is an email
        return strtolower(trim($from));
    }

    /**
     * Extract .ics attachments from email
     */
    private function extractIcsAttachments(string $rawEmail): array
    {
        $attachments = [];
        
        // Check if this is a multipart email
        if (!str_contains($rawEmail, 'Content-Type: multipart/')) {
            // Not multipart, try to find inline .ics
            if ($inline = $this->extractInlineIcs($rawEmail)) {
                $attachments[] = $inline;
            }
            return $attachments;
        }
        
        // Extract boundary
        if (!preg_match('/boundary="?([^"\s;]+)"?/', $rawEmail, $matches)) {
            return $attachments;
        }
        
        $boundary = $matches[1];
        
        // Split by boundary
        $parts = explode('--' . $boundary, $rawEmail);
        
        foreach ($parts as $part) {
            if ($ics = $this->extractIcsFromPart($part)) {
                $attachments[] = $ics;
            }
        }
        
        return $attachments;
    }

    /**
     * Extract .ics content from a MIME part
     */
    private function extractIcsFromPart(string $part): ?string
    {
        // Check if this part is a calendar attachment
        if (!str_contains($part, 'text/calendar') && !str_contains($part, '.ics')) {
            return null;
        }
        
        // Split headers and body
        $sections = preg_split("/\r?\n\r?\n/", $part, 2);
        
        if (count($sections) < 2) {
            return null;
        }
        
        $headers = strtolower($sections[0]);
        $body = $sections[1];
        
        // Check content encoding
        if (str_contains($headers, 'content-transfer-encoding: base64')) {
            $body = base64_decode($body);
        } elseif (str_contains($headers, 'content-transfer-encoding: quoted-printable')) {
            $body = quoted_printable_decode($body);
        }
        
        // Verify it's actually .ics content
        if (!str_contains($body, 'BEGIN:VCALENDAR')) {
            return null;
        }
        
        return trim($body);
    }

    /**
     * Extract inline .ics (simple emails without multipart)
     */
    private function extractInlineIcs(string $rawEmail): ?string
    {
        // Look for VCALENDAR block
        if (preg_match('/BEGIN:VCALENDAR.*?END:VCALENDAR/s', $rawEmail, $matches)) {
            return $matches[0];
        }
        
        return null;
    }

    /**
     * Simple validation of email address
     */
    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

