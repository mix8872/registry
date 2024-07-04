<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'collab' => [
        'host' => env('COLLAB_HOST'),
        'token' => env('COLLAB_TOKEN'),
        'pass' => env('PASS_TO_COLLAB', false),
        'hook_token' => env('COLLAB_HOOK_TOKEN'),
    ],

    'ipa' => [
        'host' => env('IPA_HOST', 'newipa.grechka.digital'),
        'ca_cert' => env('IPA_CACERT'),
        'version' => env('IPA_VERSION'),
    ],

    'gitlab' => [
        'host' => env('GITLAB_HOST'),
        'token' => env('GITLAB_TOKEN'),
    ],

    'vault' => [
        'host' => env('VAULT_HOST', 'https://vw.grechka.digital')
    ]
];
