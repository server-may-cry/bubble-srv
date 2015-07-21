<?php

$app->post('/ReqBuyBonus', function($request, $response) {
    $req = $request->getParsedBody();
/*
{
    "msgId":"123",
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "sysId":"VK",
    "userId":null,
    "extId":"123439103",
    
    "bonusId":"creditsPack01"
}
*/

    $template = [
        'ReqBuyBonus_NotRedyYet'
    ];

    return render($response, $template);
});
