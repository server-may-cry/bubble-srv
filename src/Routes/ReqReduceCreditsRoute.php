<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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
abstract class ReqReduceCreditsRoute {
    public static function post(Application $app, Request $request) {
        $req = requestData($request);
        $user = findUser( $req['userId'] );

        $user->credits -= max( $req['amount'], 0 );

        \R::store($user);

        $template = [
            'reqMsgId' => $req['msgId'],
            'userId' => $user->id,
            'credits' => $user->credits,
        ];

        return $app->json($template);
    }
}
