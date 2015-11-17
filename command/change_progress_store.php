<?php
// run once. change progress store from star to user

require __DIR__.'/../src/app.php';

$users = R::findCollection('users');
while($user = $users->next()) {
    $stars = R::findCollection('star', 'user_id = ? and level_mode = 0', [$user->id]);
    $progress = [];
    while($star = $stars->next()) {
        $progress[ $star->currentStage ][ $user->completeSubStage ] = $star->completeSubStageRecordStat;
    }
    $user->progressStandart = json_encode($progress);
    R::store($user);
}
