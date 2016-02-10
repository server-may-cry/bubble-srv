<?php

namespace social;

class VK
{
    private static $ch;
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
            return 'test mode';
        }
        if(!self::$ch) {
            self::$ch = curl_init();
        }
        $params['access_token'] = self::$token;
        $params['client_secret'] = getenv('VK_SECRET');
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

    public static function setUsersLevel(array $levels)
    {
        $prepare = [];
        foreach($levels as $user => $level) {
            $prepare[] = $user.':'.$level;
        }
        return self::send('secure.setUserLevel', [
            'levels' => implode(',', $prepare),
        ]);
    }

    public static function setUserLevel($id, $level)
    {
        return self::send('secure.setUserLevel', [
            'user_id' => $id,
            'level' => $level,
        ]);
    }

    public static function addEvent($userId, $activityId, $value = 0)
    {
        return self::send('secure.addAppEvent', [
            'user_id' => $userId,
            'activity_id' => $activityId,
            'value' => $value,
        ]);
    }
}
