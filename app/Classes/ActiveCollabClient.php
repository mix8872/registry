<?php

namespace App\Classes;

use ActiveCollab\SDK\Client;
use ActiveCollab\SDK\Token;

class ActiveCollabClient
{
    public static Client|null $client = null;

    public static function make(): Client
    {
        if (!self::$client) {
            switch (true) {
                case !$token = config('services.collab.token'):
                    throw new ActiveCollabClientException('COLLAB_TOKEN не задан');
                case !$host = config('services.collab.host'):
                    throw new ActiveCollabClientException('COLLAB_HOST не задан');
            }

            $objToken = new Token($token, $host);
            self::$client = new Client($objToken);
            self::$client->setSslVerifyPeer(false);
        }

        return self::$client;
    }
}
