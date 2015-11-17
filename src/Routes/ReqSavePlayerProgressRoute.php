<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use social\VK;

/*
{
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "sysId":"VK",
    "msgId":"123",
    "extId":"123439103",
    "userId":null,

    "reachedSubStage":"1", // номер точки на острове до которой игрок дошел за все время игры
    "currentStage":"0", // номер острова на котором игрок прошел точку в текущей сессии
    "reachedStage":"0", // номер острова до которого игрок дошел за все время игры
    "completeSubStage":"1", // номер точки на острове которую игрок прошел в текущей сессии
    "completeSubStageRecordStat":"2", // количество звезд набранных на пройденной точке(перезаписывается только в случае если новое значение больше предыдущего)
    "levelMode":"standart", // режим игры, может принимать значения "standart" (01) и "arcade" (02)
    Если режим игры "standart", то перезаписываются значения reachedStage01 reachedSubStage01, 
    которые приходят в ReqEnter`e, если же "arcade" то reachedStage02 и reachedSubStage02
}
*/
abstract class ReqSavePlayerProgressRoute {
    public static function post(Application $app, Request $request) {
        $req = requestData($request);
        $user = findUser( $req['userId'] );

        $levelMode = 0;
        $needUpdate = false;
        switch($req['levelMode']) {
            case 'standart':
                $levelMode = 0;
                if($req['reachedStage'] > $user->reachedStage01) {
                    $needUpdate = true;
                    $user->reachedStage01 = (int)$req['reachedStage'];
                    $user->reachedSubStage01 = (int)$req['reachedSubStage'];
                } elseif ($req['reachedStage'] == $user->reachedStage01) {
                    if($req['reachedSubStage'] > $user->reachedSubStage01) {
                        $needUpdate = true;
                        $user->reachedSubStage01 = (int)$req['reachedSubStage'];
                    }
                }
                break;
            /*
            case 'arcade':
                $levelMode = 1;
                if($req['reachedStage'] > $user->reachedStage02) {
                    $user->reachedStage02 = (int)$req['reachedStage'];
                    $user->reachedSubStage02 = (int)$req['reachedSubStage'];
                } elseif ($req['reachedStage'] == $user->reachedStage02) {
                    if($req['reachedSubStage'] > $user->reachedSubStage02) {
                        $needUpdate = true;
                        $user->reachedSubStage02 = (int)$req['reachedSubStage'];
                    }
                }
                break;
            */
            default:
                throw new \Exception("Unknown level mode ".$req['levelMode']);
        }

        switch($levelMode) {
            case 0:
                $progress = json_decode($user->progressStandart, true);
                break;
            default:
                throw new \Exception("Unknown level mode ".$levelMode);
        }
        $curStage = (int)$req['currentStage'];
        $subStage = (int)$req['completeSubStage'];
        $starCount = (int)$req['completeSubStageRecordStat'];
        if(
            !isset($progress[ $curStage ])
        ) {
            if( isset($progress[ $curStage-1 ]) ) {
                $progress[] = [];
            } else {
                throw new \Exception("Cannot save progress stage ".$curStage.' user id: '.$user->id);
            }
        }
        if(
            !isset($progress[ $curStage ][ $subStage ]) and
            !isset($progress[ $curStage ][ $subStage-1 ]) and
            (int)$req['completeSubStage'] !== 0 // first level dont have previos
        ) {
            throw new \Exception("Cannot save progress sub stage ".$subStage.' user id: '.$user->id);
        }
        if( !isset($progress[ $curStage ][ $subStage ]) or $progress[ $curStage ][ $subStage ] < $starCount) {
            $needUpdate = true;
            $progress[ $curStage ][ $subStage ] = $starCount;
        }

        if($needUpdate) {
            \R::store($user);
        }

        // в этом запросе ответ не имеет значения
        return $app->json('ok');
    }
}
