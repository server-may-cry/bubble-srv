<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/*
{
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "sysId":"VK",
    "msgId":"123",
    "extId":"123439103",
    "socIds":[
        soc0, soc1, soc2, soc3
    ],
    "userId":null
}
*/
class ReqUsersProgressRoute {
    public static function post(Application $app, Request $request) {
        $req = $request->request->all();
        $user = findUser( $req['userId'] );

        $friendsIds = $req['socIds'];
        if(count($friendsIds) === 0) {
            return $app->json(['usersProgress'=>array()]);
        }

        $friends = \R::find(
            'users', 
            'sys_id = '.$user->sysId.' AND ext_id IN ('.\R::genSlots( $friendsIds ).')',
            $friendsIds
        );
        $template = ['usersProgress'=>[]];

        foreach($friends as $friend) {
            $template['usersProgress'][] = [
                'userId' => $friend['id'],
                'socId' =>  $friend['extId'],
                'reachedStage01' => $friend['reachedStage01'],
                'reachedStage02' => $friend['reachedStage02'],
                'reachedSubStage01' => $friend['reachedSubStage01'],
                'reachedSubStage02' => $friend['reachedSubStage02'],
            ];
        }

        return $app->json($template);
    }
}
