<?php

namespace Database\Seeders;

use App\Models\InstagramMedia;
use App\Models\SocialAccount;
use App\Models\SocialProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo user ─────────────────────────────────────────────────────
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo Creator', 'password' => Hash::make('password')]
        );

        // ── Instagram account ─────────────────────────────────────────────
        $ig = SocialAccount::updateOrCreate(
            ['user_id' => $user->id, 'platform' => 'instagram'],
            [
                'platform_user_id' => '17841400008460056',
                'access_token'     => 'DEMO_IG_TOKEN_' . str_repeat('x', 40),
                'refresh_token'    => null,
                'token_expires_at' => now()->addDays(55),
                'status'           => 'active',
                'status_message'   => null,
                'last_synced_at'   => now()->subMinutes(5),
                'scopes'           => 'instagram_basic,instagram_manage_insights',
            ]
        );

        SocialProfile::updateOrCreate(
            ['social_account_id' => $ig->id],
            [
                'platform_user_id'    => '17841400008460056',
                'username'            => 'demo.creator',
                'display_name'        => 'Demo Creator',
                'account_type'        => 'CREATOR',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=Demo+Creator&background=e1306c&color=fff&size=128&bold=true',
                'meta'                => ['id' => '17841400008460056', 'username' => 'demo.creator', 'account_type' => 'CREATOR'],
            ]
        );

        // Seed 30 Instagram media posts
        $this->seedInstagramMedia($ig->id);

        // ── Snapchat account ──────────────────────────────────────────────
        $snap = SocialAccount::updateOrCreate(
            ['user_id' => $user->id, 'platform' => 'snapchat'],
            [
                'platform_user_id' => 'demo-snap-external-id-abc123',
                'access_token'     => 'DEMO_SNAP_TOKEN_' . str_repeat('x', 40),
                'refresh_token'    => 'DEMO_SNAP_REFRESH_' . str_repeat('x', 40),
                'token_expires_at' => now()->addHours(23),
                'status'           => 'active',
                'status_message'   => null,
                'last_synced_at'   => now()->subMinutes(5),
                'scopes'           => 'https://auth.snapchat.com/oauth2/api/user.display_name',
            ]
        );

        SocialProfile::updateOrCreate(
            ['social_account_id' => $snap->id],
            [
                'platform_user_id'    => 'demo-snap-external-id-abc123',
                'username'            => null,
                'display_name'        => 'Demo Creator 👻',
                'account_type'        => 'snapchat',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=D&background=fffc00&color=000&size=128&bold=true',
                'meta'                => [
                    'externalId'  => 'demo-snap-external-id-abc123',
                    'displayName' => 'Demo Creator 👻',
                    'bitmoji'     => ['avatarImage' => ['url' => 'https://ui-avatars.com/api/?name=D&background=fffc00&color=000&size=128&bold=true']],
                ],
            ]
        );

        $this->command->info('✓ Demo data seeded: demo@example.com / password');
        $this->command->info('  Instagram: 30 posts with fake engagement & insights');
        $this->command->info('  Snapchat: profile data synced');
    }

    private function seedInstagramMedia(int $accountId): void
    {
        // Delete old demo media for this account
        InstagramMedia::where('social_account_id', $accountId)->delete();

        $captions = [
            "Golden hour never disappoints ✨ #sunset #photography #goldenhour",
            "Morning coffee and good vibes ☕ Start your day with intention. #morningvibes #coffee",
            "Exploring the city streets 🏙️ Every corner tells a story. #urbanphotography #citylife",
            "Nature therapy 🌿 Sometimes you just need to get outside and breathe. #nature #outdoors",
            "Behind the lens 📸 Creating content that connects. #contentcreator #photography",
            "New drop incoming 🔥 Stay tuned for something special! #comingsoon #announcement",
            "Weekend adventures with the best crew 🎉 #friends #weekend #memories",
            "Chasing waterfalls 💧 Worth every step of the hike. #travel #nature #waterfall",
            "Studio session 🎵 Working on something new. The process is the reward. #music #studio",
            "Breakfast goals 🥑 Fuelling the day right. #food #healthyeating #brunch",
            "Rooftop sunsets hit different 🌆 #cityviews #sunset #rooftop",
            "Throwback to the best trip of the year 🗺️ #travel #throwback #wanderlust",
            "New collab alert 🤝 Excited to share this one with you all!",
            "Detail shot 🔍 #macro #photography #details",
            null, // no caption
            "Monday motivation 💪 Keep going, you're closer than you think. #motivation #mindset",
            "Local eats > tourist traps 🍜 #foodie #localeats #travel",
            "The edit took forever but it was worth it 🎨 #editing #contentcreation",
            "Ocean therapy 🌊 Nothing clears the mind like the sea. #ocean #beach #mindfulness",
            "Candid moment 😄 #candid #lifestyle #real",
            "Late night creative sessions 🌙 When inspiration strikes, you go with it.",
            "Festival season is here 🎡 #festival #summer #vibes",
            "Architectural beauty 🏛️ This city never ceases to amaze me. #architecture #design",
            "Cosy autumn walks 🍂 #autumn #fall #nature",
            "New gear day 📷 Excited to see what we create with this. #photography #gear",
            "Zero gravity feels 🏄 #extreme #sports #adventure",
            "Market day finds 🛍️ Supporting local always. #shoplocal #market",
            "Rainy day mood 🌧️ Tea, a good book, and zero plans. #cosy #rainydays",
            "The view from up here is everything 🏔️ #hiking #mountains #view",
            "Grateful for every moment 🙏 #gratitude #lifestyle",
        ];

        $types       = ['IMAGE', 'IMAGE', 'IMAGE', 'VIDEO', 'CAROUSEL_ALBUM'];
        $imageSeeds  = ['sunset','coffee','city','forest','camera','neon','friends','waterfall','music','breakfast','rooftop','map','collab','macro','plain','gym','ramen','art','ocean','laugh','night','festival','arch','autumn','lens','surf','market','rain','mountain','grateful'];

        foreach ($captions as $idx => $caption) {
            $type        = $types[$idx % count($types)];
            $seed        = $imageSeeds[$idx] ?? 'photo';
            $hasInsights = ($idx % 3 !== 0); // ~2/3 of posts have insights
            $postedAt    = now()->subDays($idx * 6)->subHours(rand(0, 23));

            InstagramMedia::create([
                'social_account_id'  => $accountId,
                'instagram_media_id' => 'DEMO_' . str_pad($idx + 1, 6, '0', STR_PAD_LEFT) . '_' . $accountId,
                'media_type'         => $type,
                'media_url'          => "https://picsum.photos/seed/{$seed}/800/800",
                'thumbnail_url'      => "https://picsum.photos/seed/{$seed}/400/400",
                'permalink'          => 'https://www.instagram.com/p/demo' . str_pad($idx + 1, 8, '0', STR_PAD_LEFT) . '/',
                'caption'            => $caption,
                'like_count'         => rand(42, 8420),
                'comments_count'     => rand(3, 340),
                'reach'              => $hasInsights ? rand(800, 25000)  : 0,
                'impressions'        => $hasInsights ? rand(1200, 42000) : 0,
                'posted_at'          => $postedAt,
            ]);
        }
    }
}
