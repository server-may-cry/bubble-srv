<?php

use Symfony\Component\HttpFoundation\Request;

$app->post('/ReqReduceCredits', function(Request $request) use ($app) {
    $req = $request->request->all();
/*
{
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "amount":"10",
    "sysId":"VK",
    "msgId":"123",
    "extId":"123439103",
    "userId":null
}
*/
    $user = R::findOne('users', 'id = ?', [ (int)$req['userId'] ]);

    if($user === NULL)
        throw new Exception("UserID: ".$req['userId'].' not found');

    $user->credits -= max( $req['amount'], 0 );

    R::store($user);

    $template = [
        'reqMsgId' => $req['msgId'],
        'userId' => $user->id,
        'credits' => $user->credits,
    ];

    return $app->json($template);
});
