<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialAccountFactory extends Factory
{
    protected $model = SocialAccount::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'platform'         => 'instagram',
            'platform_user_id' => (string) fake()->numerify('##################'),
            'access_token'     => fake()->sha256() . fake()->sha256(),
            'refresh_token'    => null,
            'token_expires_at' => now()->addDays(55),
            'status'           => 'active',
            'status_message'   => null,
            'last_synced_at'   => null,
            'scopes'           => 'instagram_business_basic',
        ];
    }

    public function instagram(): static
    {
        return $this->state(['platform' => 'instagram']);
    }

    public function snapchat(): static
    {
        return $this->state([
            'platform'      => 'snapchat',
            'refresh_token' => fake()->sha256(),
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'status'           => 'expired',
            'status_message'   => 'Token expired — please reconnect',
            'token_expires_at' => now()->subDay(),
        ]);
    }

    public function withError(): static
    {
        return $this->state([
            'status'         => 'error',
            'status_message' => 'API error occurred during sync',
        ]);
    }
}
