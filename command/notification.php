<?php

use social\VK;

require __DIR__.'/../src/app.php';

$map = [
    0 => 'Скорее возвращайтесь в игру, шаропузик соскучился!',
    1 => 'В игре обновились ВСЕ уровни, ваш ежедневный бонус ждет вас в игре!',
];

if(!isset($map[$argv[1]])) {
    die('Unknown index '.$argv[1]);
}
$msg =  $map[$argv[1]];

$users = R::findCollection('users', 'sys_id = ?', [1]);
$ids = [];
$rst = [];
while($user = $users->next()) {
    $ids[] = $user->extId;
    if(count($ids) == 200) {
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
$r = VK::sendNotification($ids, $msg);
var_dump($r);
$p = json_decode($r, true);
if (isset($p['response'])) {
    $sp = explode(',', $p['response']);
    $rst = array_merge($rst, $sp);
}
$ids = [];
sleep(5);
var_dump(count($rst));