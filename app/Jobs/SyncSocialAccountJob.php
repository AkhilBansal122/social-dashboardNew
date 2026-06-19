<?php

namespace App\Jobs;

use App\Exceptions\SocialApiException;
use App\Models\SocialAccount;
use App\Services\Instagram\InstagramApiService;
use App\Services\Snapchat\SnapchatApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSocialAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public readonly SocialAccount $account) {}

    public function handle(InstagramApiService $instagramApi, SnapchatApiService $snapchatApi): void
    {
        try {
            match ($this->account->platform) {
                'instagram' => $this->syncInstagram($instagramApi),
                'snapchat'  => $this->syncSnapchat($snapchatApi),
                default     => Log::warning("Unknown platform: {$this->account->platform}"),
            };
        } catch (SocialApiException $e) {
            Log::error("Sync failed [{$this->account->platform}]", [
                'account_id' => $this->account->id,
                'message'    => $e->getMessage(),
                'type'       => $e->type,
            ]);

            if ($e->isTokenError()) {
                $this->account->markExpired($e->getMessage());
                $this->fail($e); // no retries for auth errors
            } else {
                $this->account->markError($e->getMessage());
                throw $e; // allow retry for transient network errors
            }
        } catch (\Exception $e) {
            Log::error("Unexpected sync error [{$this->account->platform}]", ['error' => $e->getMessage()]);
            $this->account->markError('An unexpected error occurred during sync.');
            throw $e;
        }
    }

    private function syncInstagram(InstagramApiService $api): void
    {
        $api->fetchAndStoreProfile($this->account);
        $api->fetchAndStoreMedia($this->account);
        $this->account->markActive();
    }

    private function syncSnapchat(SnapchatApiService $api): void
    {
        $api->fetchAndStoreProfile($this->account);
        $this->account->markActive();
    }
}
