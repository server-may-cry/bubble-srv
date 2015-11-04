<?php
// run once before deploy srv function set user level

use social\VK;

require __DIR__.'/../src/app.php';

$users = R::findCollection('users', 'sys_id = ?', [1]);
$levels = [];
while($user = $users->next()) {
	$level = 0;
	if($user->reachedStage01) {
		$level = $user->reachedStage01 * 14 - 6;
	}
    $levels[$user->extId] = $level + $user->reachedSubStage01;
    if(count($levels) == 200) {
        $r = VK::setUsersLevel($levels);
        var_dump($r);
        $levels = [];
        sleep(1);
    }
}
$r = VK::setUsersLevel($levels);
var_dump($r);
