<?php

namespace Tests\Feature;

use App\Models\InstagramMedia;
use App\Models\SocialAccount;
use App\Models\SocialProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAccountLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_oauth_routes_require_auth(): void
    {
        $this->get('/auth/instagram')->assertRedirect('/login');
        $this->get('/auth/snapchat')->assertRedirect('/login');
    }

    public function test_missing_instagram_credentials_shows_error(): void
    {
        config(['social.instagram.client_id' => '', 'social.demo_mode' => false]);
        $user = User::factory()->create();
        $this->actingAs($user)->get('/auth/instagram')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');
    }

    public function test_missing_snapchat_credentials_shows_error(): void
    {
        config(['social.snapchat.client_id' => '', 'social.demo_mode' => false]);
        $user = User::factory()->create();
        $this->actingAs($user)->get('/auth/snapchat')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');
    }

    public function test_demo_mode_blocks_oauth(): void
    {
        config(['social.demo_mode' => true]);
        $user = User::factory()->create();
        $this->actingAs($user)->get('/auth/instagram')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');
    }

    public function test_instagram_callback_with_oauth_error_redirects(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->withSession(['instagram_oauth_state' => 'state123'])
            ->get('/auth/instagram/callback?error=access_denied&error_description=User+denied&state=state123')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');
    }

    public function test_callback_with_mismatched_state_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->withSession(['instagram_oauth_state' => 'correct'])
            ->get('/auth/instagram/callback?code=abc&state=wrong')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');
    }

    public function test_access_token_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $account = SocialAccount::create([
            'user_id'      => $user->id,
            'platform'     => 'instagram',
            'access_token' => 'plain-secret-token',
            'status'       => 'active',
        ]);

        $raw = \DB::table('social_accounts')->where('id', $account->id)->value('access_token');
        $this->assertNotEquals('plain-secret-token', $raw);
        $this->assertEquals('plain-secret-token', SocialAccount::find($account->id)->access_token);
    }

    public function test_refresh_token_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $account = SocialAccount::create([
            'user_id'       => $user->id,
            'platform'      => 'snapchat',
            'access_token'  => 'access',
            'refresh_token' => 'my-refresh-secret',
            'status'        => 'active',
        ]);

        $raw = \DB::table('social_accounts')->where('id', $account->id)->value('refresh_token');
        $this->assertNotEquals('my-refresh-secret', $raw);
        $this->assertEquals('my-refresh-secret', SocialAccount::find($account->id)->refresh_token);
    }

    public function test_one_account_per_platform_per_user(): void
    {
        $user = User::factory()->create();
        SocialAccount::create(['user_id' => $user->id, 'platform' => 'instagram', 'access_token' => 'tok1', 'status' => 'active']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        SocialAccount::create(['user_id' => $user->id, 'platform' => 'instagram', 'access_token' => 'tok2', 'status' => 'active']);
    }

    public function test_disconnect_removes_account_profile_and_media(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);
        SocialProfile::factory()->create(['social_account_id' => $account->id]);
        InstagramMedia::factory()->count(5)->create(['social_account_id' => $account->id]);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Dashboard::class)
            ->call('disconnect', 'instagram');

        $this->assertDatabaseMissing('social_accounts', ['id' => $account->id]);
        $this->assertDatabaseMissing('social_profiles', ['social_account_id' => $account->id]);
        $this->assertCount(0, InstagramMedia::where('social_account_id', $account->id)->get());
    }
}
