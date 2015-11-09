<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use config\UserParams;

/*
{
    "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
    "sysId":"VK",
    "msgId":"123",
    "extId":"123439103",
    "userId":null
}
*/
class ReqReduceTriesRoute {
    public static function post(Application $app, Request $request) {
        $user = findUser( $req['userId'] );

        $timestamp = time();
        if ($user->restoreTriesAt != 0 and $timestamp >= $user->restoreTriesAt) {
            $user->remainingTries = max($user->remainingTries, UserParams::DEFAULT_REMAINING_TRIES);
        }

        $user->remainingTries = max($user->remainingTries-1, 0);
        if ($user->restoreTriesAt == 0) {
            $user->restoreTriesAt = $timestamp + UserParams::INTERVAL_TRIES_RESTORATION;
        }

        \R::store($user);

        $template = [
            $user->remainingTries
        ];

        return $app->json($template);
    }
}
