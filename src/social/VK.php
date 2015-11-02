<?php

namespace social;

class VK
{
    private static $ch;
    private static $token;
    const URL = 'https://api.vk.com/method/';
    private static function send($method, array $params)
    {
        if(!self::$ch) {
            self::$ch = curl_init();
        }
        if(!self::$token) {
            $result = file_get_contents('https://oauth.vk.com/access_token?client_id='.getenv('VK_APP_ID').'&client_secret='.getenv('VK_SECRET').'&v=5.37&grant_type=client_credentials');
            self::$token = json_decode($result, true)['access_token'];
        }
        $params['access_token'] = self::$token;
        $params['client_secret'] = getenv('VK_SECRET');
        var_dump($params);
        curl_setopt(
            self::$ch,
            CURLOPT_URL,
            static::URL.$method.'?'.http_build_query($params)
        );
        curl_setopt(
            self::$ch,
            CURLOPT_RETURNTRANSFER,
            true
        );
        return curl_exec(self::$ch);
    }

    public static function sendNotification(array $ids, $message)
    {
        return self::send('secure.sendNotification', [
            'user_ids' => implode(',', $ids),
            'message' => $message,
        ]);
    }

    public static function setUserLevel(array $levels)
    {
        $prepare = [];
        foreach($levels as $user => $level) {
            $prepare[] = $user.':'.$level;
        }
        return self::send('secure.setUserLevel', [
            'levels' => implode(',', $prepare),
        ]);
    }

    public static function addEvent($userId, $activityId, $value)
    {
        return self::send('secure.addAppEvent', [
            'user_id' => $userId,
            'activity_id' => $activityId,
            'value' => $value,
        ]);
    }
}
