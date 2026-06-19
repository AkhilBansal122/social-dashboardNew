<?php

namespace App\Services\Snapchat;

use App\Exceptions\SocialAuthException;
use App\Models\SocialAccount;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SnapchatOAuthService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 15]);
    }

    public function getAuthorizationUrl(string $state): string
    {
        $params = http_build_query([
            'client_id'     => config('social.snapchat.client_id'),
            'redirect_uri'  => config('social.snapchat.redirect_uri'),
            'scope'         => implode(' ', config('social.snapchat.scopes', [])),
            'response_type' => 'code',
            'state'         => $state,
        ]);

        return config('social.snapchat.auth_url') . '?' . $params;
    }

    public function exchangeCodeForToken(string $code): array
    {
        try {
            $credentials = base64_encode(
                config('social.snapchat.client_id') . ':' . config('social.snapchat.client_secret')
            );

            $response = $this->http->post(config('social.snapchat.token_url'), [
                'headers'     => ['Authorization' => 'Basic ' . $credentials],
                'form_params' => [
                    'grant_type'   => 'authorization_code',
                    'redirect_uri' => config('social.snapchat.redirect_uri'),
                    'code'         => $code,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['error'])) {
                throw new SocialAuthException(
                    $body['error_description'] ?? 'Token exchange failed',
                    'snapchat'
                );
            }

            if (empty($body['access_token'])) {
                throw new SocialAuthException('No access token returned by Snapchat.', 'snapchat');
            }

            return $body;

        } catch (SocialAuthException $e) {
            throw $e;
        } catch (GuzzleException $e) {
            Log::error('Snapchat token exchange network error', ['error' => $e->getMessage()]);
            throw new SocialAuthException(
                'Network error while connecting Snapchat. Please try again.',
                'snapchat', 'network_error', $e
            );
        }
    }

    public function connectAccount(User $user, array $tokenData): SocialAccount
    {
        $expiresAt = ! empty($tokenData['expires_in'])
            ? now()->addSeconds((int) $tokenData['expires_in'])
            : null;

        return SocialAccount::updateOrCreate(
            ['user_id' => $user->id, 'platform' => 'snapchat'],
            [
                'access_token'     => $tokenData['access_token'],
                'refresh_token'    => $tokenData['refresh_token'] ?? null,
                'token_expires_at' => $expiresAt,
                'status'           => 'active',
                'status_message'   => null,
                'scopes'           => $tokenData['scope'] ?? null,
            ]
        );
    }

    public function refreshToken(SocialAccount $account): bool
    {
        if (! $account->refresh_token) {
            $account->markExpired('No refresh token available — please reconnect.');
            return false;
        }

        try {
            $credentials = base64_encode(
                config('social.snapchat.client_id') . ':' . config('social.snapchat.client_secret')
            );

            $response = $this->http->post(config('social.snapchat.token_url'), [
                'headers'     => ['Authorization' => 'Basic ' . $credentials],
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $account->refresh_token,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['error'])) {
                $account->markExpired($body['error_description'] ?? 'Token refresh failed');
                return false;
            }

            $account->update([
                'access_token'     => $body['access_token'],
                'refresh_token'    => $body['refresh_token'] ?? $account->getRawOriginal('refresh_token'),
                'token_expires_at' => isset($body['expires_in']) ? now()->addSeconds($body['expires_in']) : null,
                'status'           => 'active',
                'status_message'   => null,
            ]);

            return true;

        } catch (GuzzleException $e) {
            Log::error('Snapchat token refresh failed', ['error' => $e->getMessage()]);
            $account->markExpired('Could not refresh token — please reconnect.');
            return false;
        }
    }
}
