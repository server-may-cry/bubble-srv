<?php
// run once. change progress store from star to user

require __DIR__.'/../src/app.php';

use config\IslandLevels;

$islandsLevelCount = [
    array_fill(0,IslandLevels::$count1,-1),
    array_fill(0,IslandLevels::$count2,-1),
    array_fill(0,IslandLevels::$count3,-1),
    array_fill(0,IslandLevels::$count4,-1),
    array_fill(0,IslandLevels::$count5,-1),
    array_fill(0,IslandLevels::$count6,-1),
    array_fill(0,IslandLevels::$count7,-1),
];

$users = R::findCollection('users');
while($user = $users->next()) {
    $stars = R::findCollection('star', 'user_id = ? and level_mode = 0', [$user->id]);
    $progress = $islandsLevelCount;
    while($star = $stars->next()) {
        $progress[ $star->currentStage ][ $star->completeSubStage ] = $star->completeSubStageRecordStat;
    }
    $user->progressStandart = json_encode($progress);
    R::store($user);
}
