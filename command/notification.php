<?php

use social\VK;

require __DIR__.'/../src/app.php';

$map = [
    0 => 'Скорее возвращайтесь в игру, шаропузик соскучился!',
];

if(!isset($map[$argv[1]])) {
    die('Unknown index '.$argv[1]);
}
$msg =  $map[$argv[1]];

$users = R::findCollection('users', 'sys_id = ?', [1]);
$ids = [];
while($user = $users->next()) {
    $ids[] = $user->extId;
    if(count($ids) == 200) {
        $r = VK::sendNotification($ids, $msg);
        var_dump($r);
        $ids = [];
        sleep(1);
    }
}
$r = VK::sendNotification($ids, $msg);
var_dump($r);
