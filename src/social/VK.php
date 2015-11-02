<?php

namespace social;

class VK
{
    private static $ch;
    const URL = 'https://api.vk.com/method/';
    private static function send($method, array $params)
    {
        if(!self::$ch) {
            self::$ch = curl_init();
        }
        curl_setopt(
            self::$ch,
            CURLOPT_URL,
            static::URL.$method.'?'.http_build_query($params)
        )
        curl_setopt(
            self::$ch,
            CURLOPT_RETURNTRANSFER,
            true
        )
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
