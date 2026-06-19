# ⚡ Social Dashboard

A **Laravel 12 + Livewire 3** platform that lets creators connect Instagram and Snapchat accounts and view synced profile/content data in one unified dashboard.

---

## 🚀 Quick Start (Demo Mode — no API keys needed)

```bash
# 1. Clone & install
git clone <repo-url> social-dashboard && cd social-dashboard
composer install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Enable demo mode
echo "SOCIAL_DEMO_MODE=true" >> .env

# 4. Database (MySQL)
mysql -u root -p -e "CREATE DATABASE social_dashboard CHARACTER SET utf8mb4;"
# Edit .env: set DB_USERNAME, DB_PASSWORD

# 5. Migrate & seed
php artisan migrate
php artisan db:seed

# 6. Start
php artisan serve
```

Open **http://localhost:8000** and log in with:
- **Email:** `demo@example.com`
- **Password:** `password`

You'll see **pre-seeded demo data** — 30 Instagram posts with fake engagement metrics, insights, grid/list view — all without needing real API credentials.

---

## 🔑 Connecting Real Accounts

### Instagram (Meta Graph API)

1. Go to [developers.facebook.com](https://developers.facebook.com) → **Create App** → **Instagram Basic Display**
2. Add yourself as a Test User
3. Generate a token for the test user
4. Set the OAuth redirect URI to: `http://localhost:8000/auth/instagram/callback`
5. Update `.env`:

```env
SOCIAL_DEMO_MODE=false
INSTAGRAM_CLIENT_ID=your_app_id
INSTAGRAM_CLIENT_SECRET=your_app_secret
INSTAGRAM_REDIRECT_URI=http://localhost:8000/auth/instagram/callback
INSTAGRAM_API_VERSION=v21.0
```

> **Note:** Instagram Basic Display API only works with approved test users or a published app. Business/Creator accounts also get reach & impressions insights.

### Snapchat (Snap Kit)

1. Go to [kit.snapchat.com](https://kit.snapchat.com) → **Create App** → enable **Login Kit**
2. Add redirect URI: `http://localhost:8000/auth/snapchat/callback`
3. Update `.env`:

```env
SNAPCHAT_CLIENT_ID=your_client_id
SNAPCHAT_CLIENT_SECRET=your_client_secret
SNAPCHAT_REDIRECT_URI=http://localhost:8000/auth/snapchat/callback
```

---

## ⚙️ Full Setup

### Requirements
- PHP 8.2+
- Composer
- MySQL 8+

### Environment variables

| Variable | Description |
|---|---|
| `SOCIAL_DEMO_MODE` | `true` = use seeded demo data, no OAuth needed |
| `INSTAGRAM_CLIENT_ID` | Meta app client ID |
| `INSTAGRAM_CLIENT_SECRET` | Meta app client secret |
| `INSTAGRAM_REDIRECT_URI` | Must match Meta developer console |
| `INSTAGRAM_API_VERSION` | e.g. `v21.0` |
| `SNAPCHAT_CLIENT_ID` | Snap Kit client ID |
| `SNAPCHAT_CLIENT_SECRET` | Snap Kit client secret |
| `SNAPCHAT_REDIRECT_URI` | Must match Snap Kit console |

### Queue worker (for background sync)

```bash
php artisan queue:work
```

### Scheduled sync (all active accounts, hourly)

```bash
php artisan social:sync-all        # manual trigger
php artisan schedule:run           # run scheduler
```

---

## 🧪 Running Tests

Tests use an **in-memory SQLite** database — no MySQL needed.

```bash
php artisan test
# or
./vendor/bin/phpunit
```

| Test Suite | Coverage |
|---|---|
| `AuthTest` | Register, login, logout, guest guard |
| `SocialAccountLinkingTest` | OAuth state, missing creds, demo mode, token encryption, unique constraint, disconnect cascade |
| `DataSyncTest` | Profile sync, media sync, token expiry → `expired`, network error → `error`, Livewire sync button, pagination, queue |
| `SocialAccountModelTest` | Encryption at rest, status transitions, expiry helpers, platform labels |

---

## 🗂 Project Structure

```
app/
├── Console/Commands/
│   └── SyncAllSocialAccounts.php   # php artisan social:sync-all
├── Exceptions/
│   ├── SocialApiException.php      # API errors (token, network, etc.)
│   └── SocialAuthException.php     # OAuth flow errors
├── Http/Controllers/
│   ├── AuthController.php          # Login / register / logout
│   └── SocialAuthController.php    # OAuth redirect + callback handlers
├── Jobs/
│   └── SyncSocialAccountJob.php    # Queueable sync job (retries on network, fails on auth)
├── Livewire/
│   └── Dashboard.php               # Full-page Livewire component
├── Models/
│   ├── User.php
│   ├── SocialAccount.php           # Encrypted tokens, status helpers
│   ├── SocialProfile.php           # Unified profile across platforms
│   └── InstagramMedia.php          # Media with insights
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    ├── Instagram/
    │   ├── InstagramOAuthService.php  # Auth code → long-lived token
    │   └── InstagramApiService.php    # Profile + paginated media + insights
    └── Snapchat/
        ├── SnapchatOAuthService.php   # Auth code → token, refresh flow
        └── SnapchatApiService.php     # Profile fetch

config/
└── social.php          # Platform credentials, scopes, demo_mode flag

database/
├── factories/          # UserFactory, SocialAccountFactory, SocialProfileFactory, InstagramMediaFactory
├── migrations/         # 5 migrations (users, social_accounts, social_profiles, instagram_media, jobs)
└── seeders/
    ├── DatabaseSeeder.php
    └── DemoDataSeeder.php   # 30 realistic IG posts + Snapchat profile

resources/views/
├── auth/login.blade.php        # Pre-filled with demo credentials
├── auth/register.blade.php
├── layouts/app.blade.php       # Dark-mode navbar layout
└── livewire/
    ├── dashboard.blade.php     # Full dashboard with grid/list toggle, pagination
    └── pagination.blade.php    # Custom Livewire pagination
```

---

## 🧠 Design Decisions

| Decision | Rationale |
|---|---|
| **Encrypted tokens** | `Crypt::encryptString()` (AES-256-CBC) on both `access_token` and `refresh_token` via Eloquent mutators. Never stored in plaintext. |
| **`updateOrCreate` for reconnect** | Re-connecting an already-linked platform replaces the old token cleanly. |
| **One account per platform** | Enforced by a `UNIQUE(user_id, platform)` DB constraint. |
| **Demo mode** | `SOCIAL_DEMO_MODE=true` seeds realistic fake data so reviewers can evaluate the full UI without real API keys. |
| **Inline sync on dashboard** | Sync runs synchronously in the Livewire component for immediate feedback. The same logic is also wrapped in a `ShouldQueue` job for background/scheduled use. |
| **Graceful degradation** | All API errors are caught, the account status is updated, and a clear message is shown on the dashboard — the app never crashes. |
| **Extensible platform model** | To add a new platform: add a config entry in `social.php`, create `Services/{Platform}/` OAuth + API pair, add a route pair. The dashboard renders a card for every entry in `config('social.platforms')` automatically. |
| **Insights silently skipped** | Instagram media insights (`reach`, `impressions`) are only available for Business/Creator accounts. Errors from the insights endpoint are silently swallowed and counts stay at `0`. |
| **No Vite build step** | All CSS is inlined in Blade for zero build-tooling overhead. |

---

## 📸 Dashboard Features

- **Dark-mode UI** with polished platform cards
- **Grid / List toggle** for Instagram posts
- **Per-page selector** (12 / 24 / 48)
- **Sync Now** button with live loading spinner
- **Disconnect** with confirmation dialog
- **Status badges** — Connected / Token Expired / Error
- **Insight badges** on posts that have reach & impressions data
- **Reconnect button** shown automatically when token expires
- **Demo mode banner** explaining the seeded data
- **Pre-filled login form** with demo credentials

---

## License

MIT
