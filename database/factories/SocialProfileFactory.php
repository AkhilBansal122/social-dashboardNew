<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use App\Models\SocialProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialProfileFactory extends Factory
{
    protected $model = SocialProfile::class;

    public function definition(): array
    {
        $name = fake()->name();
        return [
            'social_account_id'   => SocialAccount::factory(),
            'platform_user_id'    => (string) fake()->numerify('##################'),
            'username'            => fake()->userName(),
            'display_name'        => $name,
            'account_type'        => fake()->randomElement(['PERSONAL', 'BUSINESS', 'CREATOR']),
            'profile_picture_url' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=6366f1&color=fff&size=128',
            'meta'                => [],
        ];
    }
}
