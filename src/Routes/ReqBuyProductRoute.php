<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use config\Market;

/*
{
    "userId":null.
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "sysId":"VK",
    "msgId":"123",
    "extId":"123439103",
    "productId":"creditsPack01",

    "levelMode":"arcade"
}
*/
class ReqBuyProductRoute {
    public static function post(Application $app, Request $request) {
        $req = $request->request->all();

        $user = findUser( $req['userId'] );

        Market::buy($app, $user, $req['productId']);

        $template = [
            'productId' => $req['productId'],
            'credits' => $user->credits,
        ];

        return $app->json($template);
    }
}
