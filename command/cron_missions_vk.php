<?php
// background cron job. send missions and levels user markers in SN

use social\VK;

require __DIR__.'/../src/app.php';

function sendLevelVK(array $notifs)
{
    usleep(300000);
    echo 'send levels'.PHP_EOL;
    $r = VK::setUsersLevel($notifs);
    var_dump($r);
}

function sendEventsVK(array $events)
{
    $len = count($events);
    $i = 0;
    foreach ($events as $userId => $activityId) {
        ++$i;
        $r = VK::addEvent($userId, $activityId);
        usleep(300000);
        echo 'event user '.$i.' of '.$len.PHP_EOL;
        var_dump($r);
    }
}

$events = \R::findCollection(
    'event',
    'status is null and sys_id = 1 limit 1000 offset 0'
);
$notifs = [[],[]];
while($event = $events->next()) {
    echo '. ';

    switch($event->type) {
        case 1:
            $notifs[0][ $event->extId ] = $event->value;
            break;
        case 2:
            $notifs[1][ $event->extId ] = $event->value;
            break;
        default:
            echo 'TYPE?';
            break 2;
    }
    $event->status = 1;
    \R::store($event);
    if(count($notifs[0]) === 200) {
        sendLevelVK($notifs[0]);
        $notifs[0] = [];
    }
}
if(count($notifs[0]) !== 0) {
    sendLevelVK($notifs[0]);
}
if(count($notifs[1]) !== 0) {
    sendEventsVK($notifs[0]);
}
