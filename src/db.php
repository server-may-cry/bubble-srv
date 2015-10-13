<?php

require_once ROOT.'rb.php'; // RedBeanPHP 4
require_once ROOT.'vendor/autoload.php'; // for predis client when run from cron

// http://redbeanphp.com/
$dburl = getenv('DATABASE_URL');
if(strlen($dburl)>0) {
    $dbopts = parse_url($dburl);
    R::setup('pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"].';port='.$dbopts["port"], $dbopts["user"], $dbopts["pass"]);
    R::freeze( true );
} else {
    R::setup(); // SQLite in memory
}
R::setAutoResolve( true );

$redis_exist = strlen(getenv('REDISCLOUD_URL'));
$redis = null;
if ($redis_exist) {
    $redis_p = parse_url(getenv('REDISCLOUD_URL'));
    $redis = new Predis\Client([
        'host' => $redis_p['host'],
        'port' => $redis_p['port'],
        'password' => $redis_p['pass'],
    ]);
}

function restoreLifes($redis_exist, $redis) {
	try{
	    $count = R::exec('update users set remaining_tries = 5 where remaining_tries < 5');
	    //error_log('users restored lifes: '.var_export($count, true) );
	} catch (Exception $e) {
	    error_log('db exec exception '.$e->getMessage());
	}

	if ($redis_exist) {
		// $redis->eval("return redis.call('del', unpack(redis.call('keys', 'remainingTries:*')))", 0, null);
	}
}
