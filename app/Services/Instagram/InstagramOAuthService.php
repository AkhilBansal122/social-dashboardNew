<?php

namespace App\Services\Instagram;

use App\Exceptions\SocialAuthException;
use App\Models\SocialAccount;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class InstagramOAuthService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 15, 'http_errors' => false]);
    }

    public function getAuthorizationUrl(string $state): string
    {
        $params = http_build_query([
            'client_id'     => config('social.instagram.client_id'),
            'redirect_uri'  => config('social.instagram.redirect_uri'),
            'scope'         => implode(',', config('social.instagram.scopes', [])),
            'response_type' => 'code',
            'state'         => $state,
        ]);

        return config('social.instagram.auth_url') . '?' . $params;
    }

    /**
     * Exchange authorisation code for a long-lived access token.
     */
    public function exchangeCodeForToken(string $code): array
    {
        try {
            // Step 1: short-lived token
            $response = $this->http->post(config('social.instagram.token_url'), [
                'form_params' => [
                    'client_id'     => config('social.instagram.client_id'),
                    'client_secret' => config('social.instagram.client_secret'),
                    'grant_type'    => 'authorization_code',
                    'redirect_uri'  => config('social.instagram.redirect_uri'),
                    'code'          => $code,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['error_type']) || isset($body['error'])) {
                $msg = $body['error_message'] ?? $body['error_description'] ?? 'Token exchange failed';
                throw new SocialAuthException($msg, 'instagram');
            }

            if (empty($body['access_token'])) {
                throw new SocialAuthException('No access token returned by Instagram', 'instagram');
            }

            // Step 2: exchange for long-lived token (60 days)
            return $this->toLongLived($body['access_token'], (string) ($body['user_id'] ?? ''));

        } catch (SocialAuthException $e) {
            throw $e;
        } catch (GuzzleException $e) {
            Log::error('Instagram token exchange network error', ['error' => $e->getMessage()]);
            throw new SocialAuthException(
                'Network error while connecting Instagram. Please try again.',
                'instagram', 'network_error', $e
            );
        }
    }

    private function toLongLived(string $shortToken, string $userId): array
    {
        try {
            $response = $this->http->get(config('social.instagram.long_lived_token_url'), [
                'query' => [
                    'grant_type'    => 'ig_exchange_token',
                    'client_secret' => config('social.instagram.client_secret'),
                    'access_token'  => $shortToken,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'access_token'     => $body['access_token'] ?? $shortToken,
                'expires_in'       => $body['expires_in']   ?? 5_184_000, // 60 days
                'platform_user_id' => $userId,
            ];
        } catch (GuzzleException $e) {
            Log::warning('Could not upgrade to long-lived Instagram token — using short-lived', ['error' => $e->getMessage()]);
            return [
                'access_token'     => $shortToken,
                'expires_in'       => 3_600,
                'platform_user_id' => $userId,
            ];
        }
    }

    public function connectAccount(User $user, array $tokenData): SocialAccount
    {
        $expiresAt = ! empty($tokenData['expires_in'])
            ? now()->addSeconds((int) $tokenData['expires_in'])
            : null;

        return SocialAccount::updateOrCreate(
            ['user_id' => $user->id, 'platform' => 'instagram'],
            [
                'platform_user_id' => $tokenData['platform_user_id'] ?? null,
                'access_token'     => $tokenData['access_token'],
                'refresh_token'    => null,
                'token_expires_at' => $expiresAt,
                'status'           => 'active',
                'status_message'   => null,
                'scopes'           => implode(',', config('social.instagram.scopes', [])),
            ]
        );
    }
}
