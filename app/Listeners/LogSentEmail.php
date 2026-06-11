<?php

namespace App\Listeners;

use App\Models\SentEmail;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;

class LogSentEmail
{
    /**
     * Persist a record of every system/transactional email that is sent.
     *
     * Calendar "blocker" (iMIP) invitations are intentionally skipped — they are
     * high volume and sent from the events@ mailbox, whereas system emails come
     * from info@. The text/calendar part check is a secondary safeguard.
     */
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->message;

            $fromEmail = $this->firstAddress($message->getFrom());

            // Skip calendar blocker emails (events@... or messages carrying an iCalendar part)
            if (($fromEmail && str_starts_with(strtolower($fromEmail), 'events@')) || $this->hasCalendarPart($message)) {
                return;
            }

            $toAddresses = array_map(
                fn (Address $a) => $a->getAddress(),
                array_merge($message->getTo(), $message->getCc(), $message->getBcc())
            );

            SentEmail::create([
                'to_email' => $this->firstAddress($message->getTo()) ?? ($toAddresses[0] ?? 'unknown'),
                'to_all' => count($toAddresses) > 1 ? implode(', ', $toAddresses) : null,
                'from_email' => $fromEmail,
                'subject' => $message->getSubject(),
                'html_body' => $message->getHtmlBody(),
                'text_body' => $message->getTextBody(),
                'mailer' => $event->data['mailer'] ?? null,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Logging an email must never break the actual send.
            Log::warning('Failed to record sent email', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get the first address string from a list of Symfony Address objects.
     */
    private function firstAddress(array $addresses): ?string
    {
        $first = $addresses[0] ?? null;

        return $first instanceof Address ? $first->getAddress() : null;
    }

    /**
     * Detect whether the message carries a text/calendar part (iMIP invitation).
     */
    private function hasCalendarPart($message): bool
    {
        foreach ($message->getAttachments() as $attachment) {
            $mediaType = $attachment->getMediaType().'/'.$attachment->getMediaSubtype();
            if (strtolower($mediaType) === 'text/calendar') {
                return true;
            }
        }

        $body = $message->getBody();
        if ($body !== null && stripos($body->getMediaSubtype() ?? '', 'calendar') !== false) {
            return true;
        }

        return false;
    }
}
