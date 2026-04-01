<?php

namespace App\Services;

use App\Jobs\SendMetaConversionEventJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MetaConversionsApiService
{
    public function sendEvent(string $eventName, Request $request, ?User $user = null, array $customData = []): string
    {
        $accessToken = config('services.meta_capi.access_token');
        $pixelId = config('services.meta_capi.pixel_id');

        if (!$accessToken || !$pixelId) {
            return '';
        }

        $eventId = Str::uuid()->toString();

        $userData = [
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
        ];

        // Hashed email (SHA-256, lowercase, trimmed — per Meta spec)
        if ($user && $user->email) {
            $userData['em'] = [hash('sha256', strtolower(trim($user->email)))];
        }

        // Hashed name
        if ($user && $user->name) {
            $nameParts = explode(' ', trim($user->name), 2);
            $userData['fn'] = [hash('sha256', strtolower(trim($nameParts[0])))];
            if (isset($nameParts[1])) {
                $userData['ln'] = [hash('sha256', strtolower(trim($nameParts[1])))];
            }
        }

        // External ID
        if ($user) {
            $userData['external_id'] = [hash('sha256', (string) $user->id)];
        }

        // Facebook click ID and browser ID from cookies
        $fbc = $request->cookie('_fbc');
        $fbp = $request->cookie('_fbp');
        if ($fbc) {
            $userData['fbc'] = $fbc;
        }
        if ($fbp) {
            $userData['fbp'] = $fbp;
        }

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'event_id' => $eventId,
                    'event_source_url' => $request->fullUrl(),
                    'action_source' => 'website',
                    'user_data' => $userData,
                ],
            ],
        ];

        if (!empty($customData)) {
            $payload['data'][0]['custom_data'] = $customData;
        }

        SendMetaConversionEventJob::dispatch($pixelId, $accessToken, $payload);

        return $eventId;
    }
}
