<?php

namespace App\Http\Controllers;

use App\Exceptions\SocialAuthException;
use App\Jobs\SyncSocialAccountJob;
use App\Services\Instagram\InstagramOAuthService;
use App\Services\Snapchat\SnapchatOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function __construct(
        private InstagramOAuthService $instagramOAuth,
        private SnapchatOAuthService  $snapchatOAuth,
    ) {}

    // ── Instagram ─────────────────────────────────────────────────────────

    public function redirectToInstagram(Request $request)
    {
        // Block if demo mode or no credentials configured
        if (config('social.demo_mode')) {
            return redirect('/dashboard')->with('error', 'Demo mode is active — OAuth is disabled. Use the seeded demo data instead.');
        }

        if (! config('social.instagram.client_id')) {
            return redirect('/dashboard')->with('error', 'Instagram credentials are not configured. See .env.example for setup instructions.');
        }

        $state = Str::random(40);
        session(['instagram_oauth_state' => $state]);

        return redirect($this->instagramOAuth->getAuthorizationUrl($state));
    }

    public function handleInstagramCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/dashboard')->with('error',
                'Instagram connection cancelled: ' . $request->get('error_description', 'Unknown error')
            );
        }

        if ($request->get('state') !== session('instagram_oauth_state')) {
            return redirect('/dashboard')->with('error', 'Invalid state parameter. Please try connecting again.');
        }

        if (! $request->has('code')) {
            return redirect('/dashboard')->with('error', 'No authorisation code received from Instagram.');
        }

        try {
            $tokenData = $this->instagramOAuth->exchangeCodeForToken($request->get('code'));
            $account   = $this->instagramOAuth->connectAccount(Auth::user(), $tokenData);

            SyncSocialAccountJob::dispatch($account);

            return redirect('/dashboard')->with('success', '✓ Instagram connected successfully! Syncing your data in the background…');

        } catch (SocialAuthException $e) {
            return redirect('/dashboard')->with('error', 'Could not connect Instagram: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect('/dashboard')->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    // ── Snapchat ──────────────────────────────────────────────────────────

    public function redirectToSnapchat(Request $request)
    {
        if (config('social.demo_mode')) {
            return redirect('/dashboard')->with('error', 'Demo mode is active — OAuth is disabled. Use the seeded demo data instead.');
        }

        if (! config('social.snapchat.client_id')) {
            return redirect('/dashboard')->with('error', 'Snapchat credentials are not configured. See .env.example for setup instructions.');
        }

        $state = Str::random(40);
        session(['snapchat_oauth_state' => $state]);

        return redirect($this->snapchatOAuth->getAuthorizationUrl($state));
    }

    public function handleSnapchatCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/dashboard')->with('error',
                'Snapchat connection cancelled: ' . $request->get('error_description', 'Unknown error')
            );
        }

        if ($request->get('state') !== session('snapchat_oauth_state')) {
            return redirect('/dashboard')->with('error', 'Invalid state parameter. Please try connecting again.');
        }

        if (! $request->has('code')) {
            return redirect('/dashboard')->with('error', 'No authorisation code received from Snapchat.');
        }

        try {
            $tokenData = $this->snapchatOAuth->exchangeCodeForToken($request->get('code'));
            $account   = $this->snapchatOAuth->connectAccount(Auth::user(), $tokenData);

            SyncSocialAccountJob::dispatch($account);

            return redirect('/dashboard')->with('success', '✓ Snapchat connected successfully! Syncing your data in the background…');

        } catch (SocialAuthException $e) {
            return redirect('/dashboard')->with('error', 'Could not connect Snapchat: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect('/dashboard')->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}
