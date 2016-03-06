<?php

namespace social;

use GuzzleHttp\Client;

class VK
{
    private static $client;
    private static $token = '57895b4f57895b4f57e4f638ae57c3fa0b5578957895b4f0132fa666980bed9fb30c60d'; // client_credentials
    private static $testMode = false;
    const URL = 'https://api.vk.com/method/';

    public static function setTestMode()
    {
        self::$testMode = true;
    }

    private static function send($method, array $params)
    {
        if(self::$testMode) {
            throw new \Exception('VK blocked in test mode');
        }
        if(!self::$client) {
            self::$client = new Client(
                [
                    'base_uri' => static::URL,
                ]
            );
        }
        $params['access_token'] = self::$token;
        $params['client_secret'] = getenv('VK_SECRET');
        $response = $client->get(
            $method,
            [
                'query' => $params
            ]
        );
        $body = $response->getBody();
        $arrayBody = json_decode($body, true);
        if (array_key_exists('error', $arrayBody)) {
            throw new \Exception('VK send error: '.$body);
        }

        return $body;
    }

    public static function sendNotification(array $ids, $message)
    {
        return self::send(
            'secure.sendNotification',
            [
                'user_ids' => implode(',', $ids),
                'message' => $message,
            ]
        );
    }

    public static function setUsersLevel(array $levels)
    {
        $prepare = [];
        foreach($levels as $user => $level) {
            $prepare[] = $user.':'.$level;
        }
        return self::send(
            'secure.setUserLevel',
            [
                'levels' => implode(',', $prepare),
            ]
        );
    }

    public static function setUserLevel($id, $level)
    {
        return self::send(
            'secure.setUserLevel',
            [
                'user_id' => $id,
                'level' => $level,
            ]
        );
    }

    public static function addEvent($userId, $activityId, $value = 0)
    {
        return self::send(
            'secure.addAppEvent',
            [
                'user_id' => $userId,
                'activity_id' => $activityId,
                'value' => $value,
            ]
        );
    }
}
