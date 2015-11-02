<?php

use social\VK;

require __DIR__.'/../src/app.php';

$map = [
    0 => 'Тестовая рассылка на дев сервере',
];

if(!isset($map[$argv[1]])) {
    die('Unknown index '.$argv[1]);
}
$msg =  $map[$argv[1]];

$users = R::findCollection('users', 'sys_id = ?', [1]);
$ids = [];
$r = null;
while($user = $users->next()) {
    $ids[] = $user->extId;
    if(count($ids) == 200) {
        $r = VK::sendNotification($ids, $msg);
        var_dump($r);
        $ids = [];
    }
}
$r = VK::sendNotification($ids, $msg);
var_dump($r);
