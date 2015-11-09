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
        $req = $request->request->all();
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
            default:
                throw new Exception("Unknown level mode ".$req['levelMode']);
        }

        if($needUpdate) {
            \R::store($user);
        }

        $star = \R::findOne('star', 'user_id = ? AND level_mode = ? AND current_stage = ? AND complete_sub_stage = ?',
            [
                $user->id,
                $levelMode,
                (int)$req['currentStage'],
                (int)$req['completeSubStage'],
            ]
        );

        if($star === NULL) {
            $star = \R::dispense('star');
            $star->user = $user;
            $star->levelMode = $levelMode;
            $star->currentStage = (int)$req['currentStage'];
            $star->completeSubStage = (int)$req['completeSubStage'];
            $star->completeSubStageRecordStat = (int)$req['completeSubStageRecordStat'];
            $result = \R::store($star);

            // social logic
            if($req['completeSubStageRecordStat'] > 0) {
                // social level
                $levelOrder = 0;
                if($req['currentStage'] > 0) {
                    $levelOrder = $req['currentStage'] * 14 - 6;
                }
                $levelOrder += $req['completeSubStage'] + 1;
                VK::setUserLevel($req['extId'], $levelOrder);

                // social event (island)
                if($req['completeSubStage'] == 14 or ($req['completeSubStage'] == 8 and $req['currentStage'] == 0)) {
                    $islandOrder = $req['currentStage'];
                    switch ($islandOrder) {
                        case 0:
                            $eventId = 0;
                            break;
                        case 1:
                            $eventId = 4;
                            break;
                        case 2:
                            $eventId = 5;
                            break;
                        case 3:
                            $eventId = 6;
                            break;
                        case 4:
                            $eventId = 7;
                            break;
                        case 5:
                            $eventId = 8;
                            break;
                        case 6:
                            $eventId = 9;
                            break;
                    }
                    if($eventId == 0) {
                        error_log('error: no eventId for '.$islandOrder.' island');
                    } else {
                        VK::addEvent($req['extId'], $eventId);
                    }
                }
            }

            return $app->json('added ('.var_export($result, true).')');
        } elseif($star->completeSubStageRecordStat < $req['completeSubStageRecordStat']) {
            $star->completeSubStageRecordStat = (int)$req['completeSubStageRecordStat'];
            $result = \R::store($star);
            return $app->json('updated ('.var_export($result, true).')');
        } else {
            return $app->json('less');
        }

        // в этом запросе ответ не имеет значения
        return $app->json('ok');
    }
}
