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

        // Set Content-Type with method parameter
        $headers->addParameterizedHeader(
            'Content-Type',
            'text/calendar',
            [
                'method' => $this->method,
                'name' => 'invite.ics',
                'charset' => 'UTF-8'
            ]
        );

        // Set as inline
        $headers->addTextHeader('Content-Disposition', 'inline; filename=invite.ics');

        return $headers;
    }
}

