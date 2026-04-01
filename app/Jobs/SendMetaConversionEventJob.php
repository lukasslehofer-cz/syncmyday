<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMetaConversionEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        private string $pixelId,
        private string $accessToken,
        private array $payload,
    ) {}

    public function handle(): void
    {
        $url = "https://graph.facebook.com/v21.0/{$this->pixelId}/events";

        $response = Http::post($url, array_merge($this->payload, [
            'access_token' => $this->accessToken,
        ]));

        if ($response->failed()) {
            Log::warning('Meta CAPI event failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'event_name' => $this->payload['data'][0]['event_name'] ?? 'unknown',
            ]);
        }
    }
}
