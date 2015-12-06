<?php

use social\VK;

require __DIR__.'/../src/app.php';

$map = [
    0 => 'Не останавливайтесь на достигнутом! Впереди еще много захватывающих приключений!',
];

$msg =  $map[$argv[1]];

$users = \R::findCollection(
    'users',
    'sys_id = ? and reached_stage01 < ? and notif_sendet = ? LIMIT = ?',
    [
        1,
        10, // 3,
        0,
        500,
    ]
);
$ids = [];
while($user = $users->next()) {
    $ids[] = $user->extId;
    $user->notifSendet = 1;
    \R::store($user);
    if(count($ids) === 2) {
        $r = VK::sendNotification($ids, $msg);
        var_dump($r);
        $ids = [];
        break;
        sleep(1);
    }
}
