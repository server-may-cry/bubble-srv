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
while($user = $users->next()) {
    $ids[] = $user->extId;
    if(count($ids) == 200) {
        VK::sendNotification($ids, $msg);
        $ids = [];
    }
}
VK::sendNotification($ids, $msg);
