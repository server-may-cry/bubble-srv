<?php

use Symfony\Component\HttpFoundation\Request;

$app->post('/ReqUsersProgress', function(Request $request) use ($app) {
    $req = (object) $request->request->all();
/*
{
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "sysId":"VK",
    "msgId":"123",
    "extId":"123439103",
    "socIds":[
        "soc0, soc1, soc2, soc3"
    ],
    "userId":null
}
*/

    if(!isset($req->userId))
        throw new \Exception('user id not set');
    $user = R::findOne('users', 'id = ?', [(int)$req->userId]);

    if($user === NULL)
        throw new Exception("UserID: ".$req->userId.' not found');

    $friendsIds = explode(',',  str_replace(' ', '', $req->socIds[0]));
    $friends = R::find('users', 
        ' ext_id IN ('.R::genSlots( $friendsIds ).')',
        $friendsIds);
    $template = ['usersProgress'=>[]];

    foreach($friends as $friend) {
        if( $user->sys_id != $friend['sys_id'] )
            continue;
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
});
