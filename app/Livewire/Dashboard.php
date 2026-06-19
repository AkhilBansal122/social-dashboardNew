<?php

namespace App\Livewire;

use App\Exceptions\SocialApiException;
use App\Models\InstagramMedia;
use App\Models\SocialAccount;
use App\Services\Instagram\InstagramApiService;
use App\Services\Snapchat\SnapchatApiService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public bool   $syncingInstagram = false;
    public bool   $syncingSnapchat  = false;
    public array  $messages         = [];
    public string $mediaView        = 'grid';
    public int    $perPage          = 12;

    protected $listeners = ['clearMessage'];

    // ── Computed ──────────────────────────────────────────────────────────

    #[Computed]
    public function connectedAccounts(): Collection
    {
        return Auth::user()
            ->socialAccounts()
            ->with('profile')
            ->get()
            ->keyBy('platform');
    }

    #[Computed]
    public function platforms(): array
    {
        return config('social.platforms', ['instagram', 'snapchat']);
    }

    #[Computed]
    public function instagramAccount(): ?SocialAccount
    {
        return $this->connectedAccounts->get('instagram');
    }

    #[Computed]
    public function snapchatAccount(): ?SocialAccount
    {
        return $this->connectedAccounts->get('snapchat');
    }

    #[Computed]
    public function instagramMedia(): ?LengthAwarePaginator
    {
        $account = $this->instagramAccount;
        if (! $account) return null;

        return InstagramMedia::where('social_account_id', $account->id)
            ->orderByDesc('posted_at')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function demoMode(): bool
    {
        return (bool) config('social.demo_mode');
    }

    // ── Actions ───────────────────────────────────────────────────────────

    public function syncPlatform(string $platform): void
    {
        $account = Auth::user()->getSocialAccount($platform);

        if (! $account) {
            $this->addMessage($platform, 'error', ucfirst($platform) . ' account is not connected.');
            return;
        }

        if ($platform === 'instagram') {
            $this->syncingInstagram = true;
        } else {
            $this->syncingSnapchat = true;
        }

        try {
            match ($platform) {
                'instagram' => $this->runInstagramSync($account),
                'snapchat'  => $this->runSnapchatSync($account),
                default     => null,
            };

            $account->update(['last_synced_at' => now(), 'status' => 'active', 'status_message' => null]);
            $this->addMessage($platform, 'success', '✓ Synced successfully!');

        } catch (SocialApiException $e) {
            if ($e->isTokenError()) {
                $account->markExpired($e->getMessage());
                $this->addMessage($platform, 'error', '⚠ ' . $e->getMessage());
            } else {
                $account->markError($e->getMessage());
                $this->addMessage($platform, 'error', '✗ Sync failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            $account->markError('Unexpected error during sync.');
            $this->addMessage($platform, 'error', '✗ An unexpected error occurred. Please try again.');
        } finally {
            if ($platform === 'instagram') {
                $this->syncingInstagram = false;
            } else {
                $this->syncingSnapchat = false;
            }
            // Clear computed cache so UI refreshes
            unset($this->connectedAccounts);
            $this->resetPage();
        }
    }

    public function disconnect(string $platform): void
    {
        $account = Auth::user()->getSocialAccount($platform);

        if ($account) {
            if ($platform === 'instagram') {
                $account->instagramMedia()->delete();
            }
            $account->profile()->delete();
            $account->delete();
        }

        unset($this->connectedAccounts);
        $this->addMessage($platform, 'success', '✓ ' . ucfirst($platform) . ' disconnected.');
        $this->resetPage();
    }

    public function setView(string $view): void
    {
        $this->mediaView = $view;
        $this->resetPage();
    }

    public function setPerPage(int $n): void
    {
        $this->perPage = $n;
        $this->resetPage();
    }

    public function clearMessage(string $platform): void
    {
        unset($this->messages[$platform]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function runInstagramSync(SocialAccount $account): void
    {
        $api = app(InstagramApiService::class);
        $api->fetchAndStoreProfile($account);
        $api->fetchAndStoreMedia($account);
    }

    private function runSnapchatSync(SocialAccount $account): void
    {
        $api = app(SnapchatApiService::class);
        $api->fetchAndStoreProfile($account);
    }

    private function addMessage(string $platform, string $type, string $text): void
    {
        $this->messages[$platform] = ['type' => $type, 'text' => $text];
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
