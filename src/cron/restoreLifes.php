<?php

require dirname(__DIR__).'/global.php';
require ROOT.'vendor/autoload.php';
require ROOT.'src/db.php';

try{
    $count = R::exec('update users set remaining_tries = 5 where remaining_tries < 5');
    //error_log('users restored lifes: '.var_export($count, true) );
} catch (Exception $e) {
    error_log('db exec exception '.$e->getMessage());
}

if ($redis_exist) {
	$redis->eval("return redis.call('del', unpack(redis.call('keys', 'remainingTries:*')))", 0);
}
