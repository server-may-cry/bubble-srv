<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use config\IslandLevels;
use config\UserParams;

/*
{
    "userId":null, // Идентификатор пользователя, получается с сервера приложения при входе в систему
    "appFriends":"0", // количество друзей в приложении
    "srcExtId":null, // vk_id друга откуда пришли
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "sysId":"VK",
    "extId":"123439103", // vk_id
    "msgId":"123", // unic ?
    "referer":null
}
*/
abstract class ReqEnterRoute {
    public static function post(Application $app, Request $request) {
        $req = requestData($request);

        switch($req['sysId']) {
            case 'test':
                $sysId = 0;
                break;
            case 'VK':
                $sysId = 1;
                if($req['authKey'] !== md5(getenv('VK_APP_ID').'_'.$req['extId'].'_'.getenv('VK_SECRET'))) {
                    throw new \Exception("Invalid auth key");
                }
                break;
            default:
                throw new \Exception('Unknown platform '.$req['sysId']);
        }

        $user = \R::findOne('users', 'sys_id = ? AND ext_id = ?', [ $sysId, $req['extId'] ]);

        $bonusCredits = 0;
        $userFriendsBonusCredits = 0;
        $timestamp = time();
        $firstGame = 0;
        $needUpdate = false;
        $triesRestore = 0;
        if($user === NULL) {
            $firstGame = 1;
            $user = \R::dispense('users');
            $user->sysId = $sysId;
            $user->extId = $req['extId'];
            $user->reachedStage01 = 0;
            $user->reachedSubStage01 = 0;
            $user->reachedStage02 = 0;
            $user->reachedSubStage02 = 0;
            $user->ignoreSavePointBlock = 0;
            $user->inifinityExtra00 = 0;
            $user->inifinityExtra01 = 0;
            $user->inifinityExtra02 = 0;
            $user->inifinityExtra03 = 0;
            $user->inifinityExtra04 = 0;
            $user->inifinityExtra05 = 0;
            $user->inifinityExtra06 = 0;
            $user->inifinityExtra07 = 0;
            $user->inifinityExtra08 = 0;
            $user->inifinityExtra09 = 0;
            $user->remainingTries = UserParams::DEFAULT_REMAINING_TRIES;
            $user->restoreTriesAt = 0;
            $user->credits = UserParams::DEFAULT_CREDITS;
            $user->friendsBonusCreditsTime = $timestamp;
            $user->id = \R::store($user);
        } else {
            if ($timestamp > $user->friendsBonusCreditsTime) {
                $needUpdate = true;
                $to = new \DateTime();
                $to->setTime(0, 0);
                $to->modify('+1 day');
                $to = $to->getTimestamp();
                $user->friendsBonusCreditsTime = $to;
                $userFriendsBonusCredits = 50 + $req['appFriends'] * UserParams::FRIENDS_BONUS_CREDITS_MULTIPLIER;
                $user->credits += $userFriendsBonusCredits;
            }
            if ($user->restoreTriesAt != 0 and $timestamp >= $user->restoreTriesAt) {
                $needUpdate = true;
                $user->remainingTries = max($user->remainingTries, UserParams::DEFAULT_REMAINING_TRIES);
                $user->restoreTriesAt = 0;
            } elseif ($user->restoreTriesAt != 0) {
                $triesRestore = $user->restoreTriesAt - $timestamp;
            }
        }
        if ($needUpdate) {
            \R::store($user);
        }

        $islandsLevelCount = [
            array_fill(0,IslandLevels::$count1,-1),
            array_fill(0,IslandLevels::$count2,-1),
            array_fill(0,IslandLevels::$count3,-1),
            array_fill(0,IslandLevels::$count4,-1),
            array_fill(0,IslandLevels::$count5,-1),
            array_fill(0,IslandLevels::$count6,-1),
            array_fill(0,IslandLevels::$count7,-1),
        ];

        $template = [
            'reqMsgId'=>$req['msgId'],
            'userId'=>$user->id,
            'reachedStage01'=>$user->reachedStage01, // Идентификатор уровня, до которого пользователь доиграл за все время игры в стандартном моде
            'reachedStage02'=>$user->reachedStage02, // Идентификатор подуровня, до которого пользователь доиграл за все время игры в стандартном моде
            'reachedSubStage01'=>$user->reachedSubStage01, // Идентификатор уровня, до которого пользователь доиграл за все время игры в аркадном моде
            'reachedSubStage02'=>$user->reachedSubStage02, // Идентификатор подуровня, до которого пользователь доиграл за все время игры в аркадном моде
            'ignoreSavePointBlock'=>$user->ignoreSavePointBlock, //  Может принимать значения 0 и 1
            'remainingTries'=>max($user->remainingTries, 0),
            'triesMin'=>UserParams::DEFAULT_REMAINING_TRIES,
            'triesRegenSecondsInterval'=>UserParams::INTERVAL_TRIES_RESTORATION,
            'secondsUntilTriesRegen'=>$triesRestore,
            'credits'=>max($user->credits,0),
            'inifinityExtra00'=>$user->inifinityExtra00, // Целое положительное число
            'inifinityExtra01'=>$user->inifinityExtra01, // Целое положительное число
            'inifinityExtra02'=>$user->inifinityExtra02, // Целое положительное число
            'inifinityExtra03'=>$user->inifinityExtra03, // Целое положительное число
            'inifinityExtra04'=>$user->inifinityExtra04, // Целое положительное число
            'inifinityExtra05'=>$user->inifinityExtra05, // Целое положительное число
            'inifinityExtra06'=>$user->inifinityExtra06, // Целое положительное число
            'inifinityExtra07'=>$user->inifinityExtra07, // Может принимать значения 0 и 1
            'inifinityExtra08'=>$user->inifinityExtra08, // Может принимать значения 0 и 1
            'inifinityExtra09'=>$user->inifinityExtra09, // Может принимать значения 0 и 1
            'bonusCredits'=>$bonusCredits, // Количество монет в 12-часовом бонусе, сейчас это 1000(возможно надо будет куда-нибудь вынести как параметр)
            'appFriendsBonusCredits'=>$userFriendsBonusCredits, // Количество монет в ежедневном бонусе за друзей. Рассчитывается по формуле reqEnter.appFriends умноженное на 30 монет за друга
            'offerAvailable'=>0, // Может принимать значения 0 и 1. Включать ли акцию сегодня или нет(возможно надо будет куда-нибудь вынести как параметр)
            'firstGame'=>$firstGame, // Может принимать значения 0 и 1. Если пользователь зашел в игру в первый раз в жизни, то 1, в остальных случаях 0.
            'stagesProgressStat01'=>[], // unsigned integer array // Список чисел. Каждое число обозначает количество игроков дошедших до определенного уровня в стандартном моде. // острова
            'stagesProgressStat02'=>[], // Список объектов subStagesRecordStat. Отображает количество звезд на подуровнях в стандартном моде.
            
            // индекс первого массива это reachedStage, а во втором массиве это reachedSubStage, а самое значение в массиве это reqSavePlayerProgress.completeSubStageRecordStat
            'subStagesRecordStats01'=>$islandsLevelCount,
            'subStagesRecordStats02'=>$islandsLevelCount,
        ];

        $collectionStars = \R::findCollection('star', 'user_id = ?', [$user->id]);
        while( $star = $collectionStars->next() ) {
            switch( $star->levelMode ) {
                case 0: // standart
                    $key = 'subStagesRecordStats01';
                    break;
                case 1: // arcade
                    $key = 'subStagesRecordStats02';
                    break;
                default:
                    error_log('error: Unknown game type in DB: '.$star->levelMode);
                    continue 2;
            }
            $template [ $key ] [ $star->currentStage ] [ $star->completeSubStage ] = $star->completeSubStageRecordStat;
        }


        $redisStandartLevels = [];
        if(isset($app['predis'])) {
            $redisStandartLevels = $app['predis']->hgetall('standart_levels');
        }
        if(count($redisStandartLevels) ) {
            $template['stagesProgressStat01'] = array_map('intval', array_values($redisStandartLevels) );
        } else {
            $usersProgresStandartRaw = \R::getAll('select count(*) as "count", reached_stage01 from users
                where reached_stage01 > 0
             group by reached_stage01 order by reached_stage01 desc;');
            $usersProgresStandart = [];
            $prevC = 0;
            foreach($usersProgresStandartRaw as $row) {
                if(count($usersProgresStandart) == 0) {
                    $usersProgresStandart[] = $row['count'];

                    $prevC = $row['count'];
                    $prevI = $row['reached_stage01'];
                }

                while($prevI >= ($row['reached_stage01'] + 1) ) {
                    --$prevI;
                    $usersProgresStandart[] = $prevC;
                }

                $prevC += $row['count'];
            }
            $usersProgresStandart[] = $prevC;
            $usersProgresStandart = array_reverse($usersProgresStandart);
            $template['stagesProgressStat01'] = $usersProgresStandart;
            if(isset($app['predis']) and count($usersProgresStandart)) {
                $toRedis = [];
                foreach($usersProgresStandart as $k => $count) {
                    $toRedis[ (string)$k ] = (string)$count;
                }
                $app['predis']->hmset('standart_levels', $toRedis);
                $app['predis']->expire('standart_levels', REDIS_CACHE_TIME_ISLANDS);
            }
        }

        $redisArcadeLevels = [];
        if(isset($app['predis'])) {
            $redisArcadeLevels = $app['predis']->hgetall('arcade_levels');
        }
        if(count($redisArcadeLevels) ) {
            $template['stagesProgressStat02'] = array_map('intval', array_values($redisArcadeLevels) );
        } else {
            $usersProgresArcadeRaw = \R::getAll('select count(*) as "count", reached_stage02 from users
                where reached_stage02 > 0
             group by reached_stage02 order by reached_stage02 desc;');
            $usersProgresArcade = [];
            $prevC = 0;
            foreach($usersProgresArcadeRaw as $row) {
                if(count($usersProgresArcade) == 0) {
                    $usersProgresArcade[] = $row['count'];

                    $prevC = $row['count'];
                    $prevI = $row['reached_stage02'];
                }

                while($prevI >= ($row['reached_stage02'] + 1) ) {
                    --$prevI;
                    $usersProgresArcade[] = $prevC;
                }

                $prevC += $row['count'];
            }
            $usersProgresArcade[] = $prevC;
            $usersProgresArcade = array_reverse($usersProgresArcade);
            $template['stagesProgressStat02'] = $usersProgresArcade;
            if(isset($app['predis']) and count($usersProgresArcade)) {
                $toRedis = [];
                foreach($usersProgresArcade as $k => $count) {
                    $toRedis[ (string)$k ] = (string)$count;
                }
                $app['predis']->hmset('arcade_levels', $toRedis);
                $app['predis']->expire('arcade_levels', REDIS_CACHE_TIME_ISLANDS);
            }
        }

        return $app->json($template);
    }
}
