<?php

namespace Database\Factories;

use App\Models\InstagramMedia;
use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstagramMediaFactory extends Factory
{
    protected $model = InstagramMedia::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['IMAGE', 'IMAGE', 'VIDEO', 'CAROUSEL_ALBUM']);
        $seed = fake()->word();
        return [
            'social_account_id'  => SocialAccount::factory()->instagram(),
            'instagram_media_id' => fake()->unique()->numerify('##################'),
            'media_type'         => $type,
            'media_url'          => "https://picsum.photos/seed/{$seed}/800/800",
            'thumbnail_url'      => "https://picsum.photos/seed/{$seed}/400/400",
            'permalink'          => 'https://www.instagram.com/p/' . fake()->regexify('[A-Za-z0-9_-]{11}') . '/',
            'caption'            => fake()->optional(.8)->sentence(fake()->numberBetween(5, 25)),
            'like_count'         => fake()->numberBetween(0, 8000),
            'comments_count'     => fake()->numberBetween(0, 400),
            'reach'              => fake()->optional(.6, 0)->numberBetween(500, 25000),
            'impressions'        => fake()->optional(.6, 0)->numberBetween(800, 45000),
            'posted_at'          => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
