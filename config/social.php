<?php

return [
    'platforms' => ['instagram', 'snapchat'],

    /*
    |--------------------------------------------------------------------------
    | Demo / Sandbox Mode
    |--------------------------------------------------------------------------
    | When SOCIAL_DEMO_MODE=true the app uses fake seeded data instead of
    | making real API calls. This lets reviewers evaluate the UI without
    | needing real OAuth credentials.
    */
    'demo_mode' => (bool) env('SOCIAL_DEMO_MODE', false),

    'instagram' => [
        'client_id'            => env('INSTAGRAM_CLIENT_ID', ''),
        'client_secret'        => env('INSTAGRAM_CLIENT_SECRET', ''),
        'redirect_uri'         => env('INSTAGRAM_REDIRECT_URI', env('APP_URL', 'http://localhost:8000') . '/auth/instagram/callback'),

        /*
         * Instagram Basic Display API scopes.
         * For a Business/Creator account also add:
         *   instagram_manage_insights, pages_show_list, pages_read_engagement
         */
        'scopes'               => ['instagram_basic', 'instagram_manage_insights'],

        // Meta / Instagram Graph API endpoints
        'graph_url'            => 'https://graph.instagram.com',
        'auth_url'             => 'https://api.instagram.com/oauth/authorize',
        'token_url'            => 'https://api.instagram.com/oauth/access_token',
        'long_lived_token_url' => 'https://graph.instagram.com/access_token',

        /*
         * API version (update when Meta deprecates older versions)
         * Used as prefix: https://graph.instagram.com/v21.0/me
         */
        'api_version'          => env('INSTAGRAM_API_VERSION', 'v21.0'),
    ],

    'snapchat' => [
        'client_id'     => env('SNAPCHAT_CLIENT_ID', ''),
        'client_secret' => env('SNAPCHAT_CLIENT_SECRET', ''),
        'redirect_uri'  => env('SNAPCHAT_REDIRECT_URI', env('APP_URL', 'http://localhost:8000') . '/auth/snapchat/callback'),

        /*
         * Snap Kit Login scopes.
         * https://kit.snapchat.com/docs/login-kit-web
         */
        'scopes'        => [
            'https://auth.snapchat.com/oauth2/api/user.display_name',
            'https://auth.snapchat.com/oauth2/api/user.bitmoji.avatar',
            'https://auth.snapchat.com/oauth2/api/user.external_id',
        ],

        'auth_url'  => 'https://accounts.snapchat.com/login/oauth2/authorize',
        'token_url' => 'https://accounts.snapchat.com/login/oauth2/access_token',
        'api_url'   => 'https://kit.snapchat.com/v1',
    ],
];
