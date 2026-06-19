<?php

namespace App\Services\Instagram;

use App\Exceptions\SocialApiException;
use App\Models\InstagramMedia;
use App\Models\SocialAccount;
use App\Models\SocialProfile;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class InstagramApiService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 20]);
    }

    private function base(): string
    {
        $version = config('social.instagram.api_version', 'v21.0');
        return rtrim(config('social.instagram.graph_url'), '/') . '/' . $version;
    }

    // ── Profile ───────────────────────────────────────────────────────────

    public function fetchAndStoreProfile(SocialAccount $account): SocialProfile
    {
        $data = $this->get($account, '/me', [
            'fields' => 'id,username,account_type,profile_picture_url,name',
        ]);

        $profile = SocialProfile::updateOrCreate(
            ['social_account_id' => $account->id],
            [
                'platform_user_id'    => $data['id']                  ?? null,
                'username'            => $data['username']             ?? null,
                'display_name'        => $data['name'] ?? $data['username'] ?? null,
                'account_type'        => $data['account_type']         ?? null,
                'profile_picture_url' => $data['profile_picture_url'] ?? null,
                'meta'                => $data,
            ]
        );

        if (! $account->platform_user_id && isset($data['id'])) {
            $account->update(['platform_user_id' => $data['id']]);
        }

        return $profile;
    }

    // ── Media ─────────────────────────────────────────────────────────────

    public function fetchAndStoreMedia(SocialAccount $account): int
    {
        $fields  = 'id,caption,media_type,media_url,thumbnail_url,permalink,like_count,comments_count,timestamp';
        $synced  = 0;
        $cursor  = null;

        do {
            $params = ['fields' => $fields, 'limit' => 25];
            if ($cursor) {
                $params['after'] = $cursor;
            }

            $data = $this->get($account, '/me/media', $params);

            foreach ($data['data'] ?? [] as $item) {
                $insights = $this->fetchMediaInsights($account, $item['id']);

                InstagramMedia::updateOrCreate(
                    ['instagram_media_id' => $item['id']],
                    [
                        'social_account_id' => $account->id,
                        'media_type'        => $item['media_type']     ?? null,
                        'media_url'         => $item['media_url']      ?? null,
                        'thumbnail_url'     => $item['thumbnail_url']  ?? null,
                        'permalink'         => $item['permalink']      ?? null,
                        'caption'           => $item['caption']        ?? null,
                        'like_count'        => (int) ($item['like_count']     ?? 0),
                        'comments_count'    => (int) ($item['comments_count'] ?? 0),
                        'reach'             => (int) ($insights['reach']       ?? 0),
                        'impressions'       => (int) ($insights['impressions'] ?? 0),
                        'posted_at'         => isset($item['timestamp'])
                            ? Carbon::parse($item['timestamp'])
                            : null,
                    ]
                );
                $synced++;
            }

            $cursor = $data['paging']['cursors']['after'] ?? null;
            $hasNext = isset($data['paging']['next']);

        } while ($hasNext && $cursor && $synced < 100);

        $account->update(['last_synced_at' => now()]);

        return $synced;
    }

    private function fetchMediaInsights(SocialAccount $account, string $mediaId): array
    {
        try {
            $data   = $this->get($account, "/{$mediaId}/insights", ['metric' => 'reach,impressions']);
            $result = [];
            foreach ($data['data'] ?? [] as $insight) {
                $result[$insight['name']] = $insight['values'][0]['value'] ?? $insight['value'] ?? 0;
            }
            return $result;
        } catch (\Exception) {
            return []; // Insights not available for personal accounts — silently skip
        }
    }

    // ── HTTP helper ───────────────────────────────────────────────────────

    private function get(SocialAccount $account, string $endpoint, array $params = []): array
    {
        if ($account->isTokenExpired()) {
            $account->markExpired();
            throw new SocialApiException(
                'Instagram token has expired. Please reconnect your account.',
                'instagram', 401, 'token_expired'
            );
        }

        $url = $this->base() . $endpoint;

        try {
            $response = $this->http->get($url, [
                'query' => array_merge($params, ['access_token' => $account->access_token]),
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            // Graph API returns 200 with an error body in some cases
            if (isset($body['error'])) {
                $code    = $body['error']['code'] ?? 0;
                $message = $body['error']['message'] ?? 'Unknown API error';
                if ($code === 190 || str_contains($message, 'access token')) {
                    $account->markExpired($message);
                    throw new SocialApiException($message, 'instagram', 401, 'token_expired');
                }
                throw new SocialApiException($message, 'instagram', $code, 'api_error');
            }

            return $body;

        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();
            $body   = json_decode($e->getResponse()->getBody()->getContents(), true);
            $msg    = $body['error']['message'] ?? $e->getMessage();

            Log::warning('Instagram API client error', ['account' => $account->id, 'status' => $status, 'msg' => $msg]);

            if ($status === 401 || ($body['error']['code'] ?? 0) === 190) {
                $account->markExpired($msg);
                throw new SocialApiException($msg, 'instagram', 401, 'token_expired', $e);
            }

            throw new SocialApiException($msg, 'instagram', $status, 'api_error', $e);

        } catch (SocialApiException $e) {
            throw $e;
        } catch (GuzzleException $e) {
            Log::error('Instagram API network error', ['error' => $e->getMessage()]);
            throw new SocialApiException(
                'Instagram is currently unreachable. Please try again later.',
                'instagram', 0, 'network_error', $e
            );
        }
    }
}
