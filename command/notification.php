<?php

use social\VK;

require __DIR__.'../src/app.php';

$users = R::findCollection('users', 'sys_id = ?', [1]);
$ids = [];
while($user = $users->next()) {
    $ids[] = $user->sysId;
    if(count($ids) == 200) {
        var_dump($ids);
        var_dump($argv[1]);
        die();
        VK::sendNotification($ids, $argv[1]);
        $ids = [];
    }
}
echo 'BAD';
var_dump($ids);
echo 'BAD';
var_dump($argv[1]);
die();
VK::sendNotification($ids, $argv[1]);
