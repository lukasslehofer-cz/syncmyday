<?php

namespace App\Services\Email;

use Symfony\Component\Mime\Part\AbstractPart;

/**
 * Custom Symfony Mime Part for text/calendar content
 * 
 * This extends AbstractPart to create a proper text/calendar MIME part
 * with inline disposition, which is required for Outlook to auto-process
 * calendar invitations.
 */
class CalendarPart extends AbstractPart
{
    private string $content;
    private string $method;

    public function __construct(string $content, string $method = 'REQUEST')
    {
        parent::__construct();
        $this->content = $content;
        $this->method = $method;
    }

    public function bodyToString(): string
    {
        return $this->content;
    }

    public function bodyToIterable(): iterable
    {
        yield $this->content;
    }

    public function asDebugString(): string
    {
        return 'CalendarPart';
    }

    /**
     * Return media type/subtype for this part
     */
    public function getMediaType(): string
    {
        return 'text';
    }

    public function getMediaSubtype(): string
    {
        return 'calendar';
    }

    /**
     * Prepare headers for this calendar part
     */
    public function getPreparedHeaders(): \Symfony\Component\Mime\Header\Headers
    {
        $headers = parent::getPreparedHeaders();

        // Remove default Content-Type added by parent
        if ($headers->has('Content-Type')) {
            $headers->remove('Content-Type');
        }

        // Add proper Content-Type with method and component parameters
        // component=VEVENT is required by some Outlook versions
        $headers->addParameterizedHeader(
            'Content-Type',
            'text/calendar',
            [
                'method' => $this->method,
                'component' => 'VEVENT',
                'name' => 'invite.ics',
                'charset' => 'UTF-8'
            ]
        );

        // Set as inline with method parameter
        $headers->addTextHeader('Content-Disposition', 'inline; method=' . $this->method . '; filename=invite.ics');

        // Add Exchange/Outlook specific Content-Class header
        // This signals to Outlook that this is a calendar message
        $headers->addTextHeader('Content-Class', 'urn:content-classes:calendarmessage');

        // Force base64 encoding for better Outlook compatibility
        $headers->addTextHeader('Content-Transfer-Encoding', 'base64');

        return $headers;
    }
}

