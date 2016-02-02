<?php

use social\VK;

require __DIR__.'/../src/app.php';

$map = [
    0 => 'Не останавливайтесь на достигнутом! Впереди еще много захватывающих приключений!',
];

$msg =  $map[$argv[1]];

$users = \R::findCollection(
    'users',
    'sys_id = ? and reached_stage01 < ? and notif_sendet = ? limit 500 offset 0',
    [
        1,
        3,
        0,
    ]
);
$rst = [];
$ids = [];
while($user = $users->next()) {
    $ids[] = $user->extId;
    $user->notifSendet = 1;
    \R::store($user);
    if(count($ids) === 200) {
        $r = VK::sendNotification($ids, $msg);
        var_dump($r);
        $p = json_decode($r, true);
        if (isset($p['response'])) {
            $sp = explode(',', $p['response']);
            $rst = array_merge($rst, $sp);
        }
        $ids = [];
        sleep(5);
    }
}
if(count($ids) !== 0) {
    $r = VK::sendNotification($ids, $msg);
    var_dump($r);
    $p = json_decode($r, true);
    if (isset($p['response'])) {
        $sp = explode(',', $p['response']);
        $rst = array_merge($rst, $sp);
    }
}
var_dump(count($rst));