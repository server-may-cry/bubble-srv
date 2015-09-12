<?php

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
$app->post('/ReqBuyProduct', function(Request $request) use ($app) {

    if( $request->request->has('userId') )
        throw new \Exception('user id not set');
    $user = findUser($request->request->get('userId') );

    Market::buy($user, $request->request->get('productId'));

    $template = [
        'productId' => $request->request->productId,
        'credits' => $user->credits,
    ];

    return $app->json($template);
});
