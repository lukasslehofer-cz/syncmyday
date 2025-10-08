<?php

namespace Database\Factories;

use App\Models\User;
use App\Services\Encryption\TokenEncryptionService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarConnectionFactory extends Factory
{
    public function definition(): array
    {
        // Set test encryption key for factory
        if (!config('services.token_encryption_key')) {
            config(['services.token_encryption_key' => base64_encode(random_bytes(32))]);
        }

        $encryptionService = new TokenEncryptionService();

        return [
            'user_id' => User::factory(),
            'provider' => fake()->randomElement(['google', 'microsoft']),
            'provider_account_id' => fake()->uuid(),
            'provider_email' => fake()->email(),
            'access_token_encrypted' => $encryptionService->encrypt(fake()->sha256()),
            'refresh_token_encrypted' => $encryptionService->encrypt(fake()->sha256()),
            'token_expires_at' => now()->addHour(),
            'available_calendars' => [
                ['id' => 'primary', 'name' => 'Primary', 'primary' => true],
                ['id' => 'work', 'name' => 'Work', 'primary' => false],
            ],
            'status' => 'active',
        ];
    }
}

