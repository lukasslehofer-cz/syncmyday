<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'locale' => 'en',
            'timezone' => 'UTC',
            'subscription_tier' => 'free',
            'remember_token' => Str::random(10),
        ];
    }

    public function pro(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_tier' => 'pro',
            'subscription_ends_at' => now()->addMonth(),
        ]);
    }
}

