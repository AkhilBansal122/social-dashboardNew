<?php

namespace App\Console\Commands;

use App\Jobs\SyncSocialAccountJob;
use App\Models\SocialAccount;
use Illuminate\Console\Command;

class SyncAllSocialAccounts extends Command
{
    protected $signature   = 'social:sync-all';
    protected $description = 'Dispatch sync jobs for all active social accounts';

    public function handle(): int
    {
        $accounts = SocialAccount::where('status', 'active')->get();
        $this->info("Dispatching sync for {$accounts->count()} active account(s)…");
        foreach ($accounts as $account) {
            SyncSocialAccountJob::dispatch($account);
            $this->line("  → Queued [{$account->platform}] account #{$account->id}");
        }
        $this->info('Done.');
        return Command::SUCCESS;
    }
}
