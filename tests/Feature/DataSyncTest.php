<?php

namespace Tests\Feature;

use App\Exceptions\SocialApiException;
use App\Jobs\SyncSocialAccountJob;
use App\Models\InstagramMedia;
use App\Models\SocialAccount;
use App\Models\SocialProfile;
use App\Models\User;
use App\Services\Instagram\InstagramApiService;
use App\Services\Snapchat\SnapchatApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class DataSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_instagram_profile_stored_on_sync(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);

        $mock = Mockery::mock(InstagramApiService::class);
        $mock->shouldReceive('fetchAndStoreProfile')->once()->andReturn(
            SocialProfile::factory()->create(['social_account_id' => $account->id])
        );
        $mock->shouldReceive('fetchAndStoreMedia')->once()->andReturn(5);
        $this->app->instance(InstagramApiService::class, $mock);

        $snapMock = Mockery::mock(SnapchatApiService::class);
        $this->app->instance(SnapchatApiService::class, $snapMock);

        (new SyncSocialAccountJob($account))->handle($mock, $snapMock);

        $account->refresh();
        $this->assertEquals('active', $account->status);
    }

    public function test_snapchat_profile_stored_on_sync(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->snapchat()->create(['user_id' => $user->id]);

        $igMock = Mockery::mock(InstagramApiService::class);
        $this->app->instance(InstagramApiService::class, $igMock);

        $mock = Mockery::mock(SnapchatApiService::class);
        $mock->shouldReceive('fetchAndStoreProfile')->once()->andReturn(
            SocialProfile::factory()->create(['social_account_id' => $account->id])
        );
        $this->app->instance(SnapchatApiService::class, $mock);

        (new SyncSocialAccountJob($account))->handle($igMock, $mock);

        $account->refresh();
        $this->assertEquals('active', $account->status);
    }

    public function test_token_error_marks_account_expired(): void
    {
        $user    = User::factory()->create();
        $account = SyncSocialAccountJob::class;
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);

        $mock = Mockery::mock(InstagramApiService::class);
        $mock->shouldReceive('fetchAndStoreProfile')->andThrow(
            new SocialApiException('Token expired', 'instagram', 401, 'token_expired')
        );
        $this->app->instance(InstagramApiService::class, $mock);
        $snapMock = Mockery::mock(SnapchatApiService::class);
        $this->app->instance(SnapchatApiService::class, $snapMock);

        try {
            (new SyncSocialAccountJob($account))->handle($mock, $snapMock);
        } catch (\Exception) {}

        $account->refresh();
        $this->assertEquals('expired', $account->status);
    }

    public function test_network_error_marks_account_error(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);

        $mock = Mockery::mock(InstagramApiService::class);
        $mock->shouldReceive('fetchAndStoreProfile')->andThrow(
            new SocialApiException('Network unreachable', 'instagram', 0, 'network_error')
        );
        $this->app->instance(InstagramApiService::class, $mock);
        $snapMock = Mockery::mock(SnapchatApiService::class);
        $this->app->instance(SnapchatApiService::class, $snapMock);

        try {
            (new SyncSocialAccountJob($account))->handle($mock, $snapMock);
        } catch (\Exception) {}

        $account->refresh();
        $this->assertEquals('error', $account->status);
    }

    public function test_dashboard_sync_now_shows_success(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);
        SocialProfile::factory()->create(['social_account_id' => $account->id]);

        $mock = Mockery::mock(InstagramApiService::class);
        $mock->shouldReceive('fetchAndStoreProfile')->once()->andReturn($account->profile);
        $mock->shouldReceive('fetchAndStoreMedia')->once()->andReturn(10);
        $this->app->instance(InstagramApiService::class, $mock);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Dashboard::class)
            ->call('syncPlatform', 'instagram')
            ->assertSet('messages.instagram.type', 'success');
    }

    public function test_dashboard_sync_shows_error_on_token_expiry(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);

        $mock = Mockery::mock(InstagramApiService::class);
        $mock->shouldReceive('fetchAndStoreProfile')->andThrow(
            new SocialApiException('Token expired', 'instagram', 401, 'token_expired')
        );
        $this->app->instance(InstagramApiService::class, $mock);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Dashboard::class)
            ->call('syncPlatform', 'instagram')
            ->assertSet('messages.instagram.type', 'error');

        $account->refresh();
        $this->assertEquals('expired', $account->status);
    }

    public function test_media_is_paginated(): void
    {
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);
        InstagramMedia::factory()->count(30)->create(['social_account_id' => $account->id]);

        $component = \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Dashboard::class);

        $this->assertEquals(12, $component->get('perPage'));
        $this->assertEquals(30, InstagramMedia::where('social_account_id', $account->id)->count());
    }

    public function test_sync_job_dispatched_to_queue(): void
    {
        Queue::fake();
        $user    = User::factory()->create();
        $account = SocialAccount::factory()->instagram()->create(['user_id' => $user->id]);
        SyncSocialAccountJob::dispatch($account);
        Queue::assertPushed(SyncSocialAccountJob::class);
    }
}
