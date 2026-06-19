<?php

namespace Tests\Unit;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAccountModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_token_encrypted(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id, 'access_token' => 'my-plain-token']);

        $raw = \DB::table('social_accounts')->where('id', $account->id)->value('access_token');
        $this->assertNotEquals('my-plain-token', $raw);
        $this->assertEquals('my-plain-token', SocialAccount::find($account->id)->access_token);
    }

    public function test_null_refresh_token_stays_null(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id, 'refresh_token' => null]);
        $this->assertNull(SocialAccount::find($account->id)->refresh_token);
    }

    public function test_mark_expired(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);
        $account->markExpired('My expiry message');
        $account->refresh();
        $this->assertEquals('expired', $account->status);
        $this->assertEquals('My expiry message', $account->status_message);
    }

    public function test_mark_error(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);
        $account->markError('Something failed');
        $account->refresh();
        $this->assertEquals('error', $account->status);
    }

    public function test_mark_active_clears_message(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->withError()->create(['user_id' => $user->id]);
        $account->markActive();
        $account->refresh();
        $this->assertEquals('active', $account->status);
        $this->assertNull($account->status_message);
    }

    public function test_is_token_expired_past(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id, 'token_expires_at' => now()->subHour()]);
        $this->assertTrue($account->isTokenExpired());
    }

    public function test_is_token_expired_future(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id, 'token_expires_at' => now()->addDay()]);
        $this->assertFalse($account->isTokenExpired());
    }

    public function test_platform_labels(): void
    {
        $user = User::factory()->create();
        $ig   = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);
        $snap = SocialAccount::factory()->snapchat()->create(['user_id' => $user->id]);
        $this->assertEquals('Instagram', $ig->platformLabel());
        $this->assertEquals('Snapchat', $snap->platformLabel());
    }

    public function test_has_platform_connected(): void
    {
        $user = User::factory()->create();
        SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);
        $this->assertTrue($user->hasPlatformConnected('instagram'));
        $this->assertFalse($user->hasPlatformConnected('snapchat'));
    }
}
