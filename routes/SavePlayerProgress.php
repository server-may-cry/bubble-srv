<?php

$app->post('/ReqSavePlayerProgress', function($request, $response) {
    $req = $request->getParsedBody();
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
    if(!isset($req->userId))
        throw new \Exception('user id not set');
    $user = R::findOne('users', 'id = ?', [$req->userId]);

    if($user === NULL)
        throw new Exception("UserID: ".$req->userId.' not found');


    switch($req->levelMode) {
        case 'standart':
            $user->reachedStage01 = max((int)$req->reachedStage, $user->reachedStage01);
            if($req->reachedStage > $user->reachedStage01) {
                $user->reachedSubStage01 = (int)$req->reachedSubStage;
            } elseif ($req->reachedStage == $user->reachedStage01) {
                $user->reachedSubStage01 = max((int)$req->reachedSubStage, $user->reachedSubStage01);
            }
            break;
        case 'arcade':
            $user->reachedStage02 = max((int)$req->reachedStage, $user->reachedStage02);
            if($req->reachedStage > $user->reachedStage02) {
                $user->reachedSubStage02 = (int)$req->reachedSubStage;
            } elseif ($req->reachedStage == $user->reachedStage02) {
                $user->reachedSubStage02 = max((int)$req->reachedSubStage, $user->reachedSubStage02);
            }
            break;
        default:
            throw new Exception("Unknown level mode");
    }

    R::store($user);

    $star = R::findOne('star', 'user_id = ? AND level_mode = ? AND current_stage = ? AND complete_sub_stage = ?',
        [
            $user->id,
            $req->levelMode,
            (int)$req->currentStage,
            (int)$req->completeSubStage,
        ]
    );

    if($star === NULL) {
        $star = R::dispense('star');
        $star->user = $user;
        $star->levelMode = $req->levelMode;
        $star->currentStage = (int)$req->currentStage;
        $star->completeSubStage = (int)$req->completeSubStage;
        $star->completeSubStageRecordStat = (int)$req->completeSubStageRecordStat;
        $result = R::store($star);
        return render($response, 'added ('.var_export($result, true).')');
    } elseif($star->completeSubStageRecordStat < $req->completeSubStageRecordStat) {
        $star->completeSubStageRecordStat = (int)$req->completeSubStageRecordStat;
        $result = R::store($star);
        return render($response, 'updated ('.var_export($result, true).')');
    } else {
        return render($response, 'less');
    }

    // в этом запросе ответ не имеет значения
    return render($response, 'ok');
});
