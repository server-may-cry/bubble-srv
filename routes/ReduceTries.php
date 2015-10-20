<?php

use Symfony\Component\HttpFoundation\Request;

$app->post('/ReqReduceTries', function(Request $request) use ($app) {
    $req = $request->request->all();
/*
{
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "sysId":"VK",
    "msgId":"123",
    "extId":"123439103",
    "userId":null
}
*/
    if(!isset($req['userId']))
        throw new \Exception('user id not set');
    $user = R::findOne('users', 'id = ?', [ (int)$req['userId'] ]);

    if($user === NULL)
        throw new Exception("UserID: ".$req['userId'].' not found');

    $timestamp = time();
    if ($user->restoreTriesAt != 0 and $timestamp >= $user->restoreTriesAt) {
        $user->remainingTries = UserParams::$defaultUserRemainingTries;
    }

    $user->remainingTries = max($user->remainingTries-1, 0);
    if ($user->restoreTriesAt == 0) {
        $user->restoreTriesAt = $timestamp + UserParams::INTERVAL_TRIES_RESTORATION;
    }

    R::store($user);

    $template = [
        $user->remainingTries
    ];

    return $app->json($template);
});
