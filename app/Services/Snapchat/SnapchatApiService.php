<?php

namespace App\Services\Snapchat;

use App\Exceptions\SocialApiException;
use App\Models\SocialAccount;
use App\Models\SocialProfile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SnapchatApiService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 20]);
    }

    public function fetchAndStoreProfile(SocialAccount $account): SocialProfile
    {
        // Attempt token refresh if expired
        if ($account->isTokenExpired()) {
            if ($account->refresh_token) {
                $refreshed = app(SnapchatOAuthService::class)->refreshToken($account);
                if (! $refreshed) {
                    throw new SocialApiException(
                        'Snapchat token expired. Please reconnect.',
                        'snapchat', 401, 'token_expired'
                    );
                }
                $account->refresh();
            } else {
                $account->markExpired();
                throw new SocialApiException(
                    'Snapchat token expired. Please reconnect.',
                    'snapchat', 401, 'token_expired'
                );
            }
        }

        $body = $this->get($account, '/me');

        // Snap Kit response structure: { data: { me: { ... } } }
        $me = $body['data']['me'] ?? $body;

        $profile = SocialProfile::updateOrCreate(
            ['social_account_id' => $account->id],
            [
                'platform_user_id'    => $me['externalId']   ?? $me['external_id']   ?? null,
                'username'            => null,
                'display_name'        => $me['displayName']  ?? $me['display_name']  ?? null,
                'account_type'        => 'snapchat',
                'profile_picture_url' => $me['bitmoji']['avatarImage']['url']
                                        ?? $me['bitmoji_avatar_url']
                                        ?? null,
                'meta'                => $me,
            ]
        );

        if (! $account->platform_user_id && $profile->platform_user_id) {
            $account->update(['platform_user_id' => $profile->platform_user_id]);
        }

        return $profile;
    }

    private function get(SocialAccount $account, string $endpoint): array
    {
        $url = rtrim(config('social.snapchat.api_url'), '/') . $endpoint;

        try {
            $response = $this->http->get($url, [
                'headers' => ['Authorization' => 'Bearer ' . $account->access_token],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();
            $body   = json_decode($e->getResponse()->getBody()->getContents(), true);
            $msg    = $body['error_description'] ?? $body['message'] ?? $e->getMessage();

            Log::warning('Snapchat API client error', ['account' => $account->id, 'status' => $status, 'msg' => $msg]);

            if ($status === 401) {
                $account->markExpired($msg);
                throw new SocialApiException($msg, 'snapchat', 401, 'token_expired', $e);
            }

            throw new SocialApiException($msg, 'snapchat', $status, 'api_error', $e);

        } catch (GuzzleException $e) {
            Log::error('Snapchat API network error', ['error' => $e->getMessage()]);
            throw new SocialApiException(
                'Snapchat is currently unreachable. Please try again later.',
                'snapchat', 0, 'network_error', $e
            );
        }
    }
}
