<?php

require_once ROOT.'rb.php'; // RedBeanPHP 4

// http://redbeanphp.com/
$dburl = getenv('DATABASE_URL');
if(strlen($dburl)>0) {
    $dbopts = parse_url($dburl);
    R::setup('pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"].';port='.$dbopts["port"], $dbopts["user"], $dbopts["pass"]);
} else {
    R::setup(); // SQLite in memory
}
R::setAutoResolve( true );

$redis_exist = strlen(getenv('REDISCLOUD_URL'));
if ($redis_exist) {
    $redis_p = parse_url(getenv('REDISCLOUD_URL'));
    $redis = new Predis\Client([
        'host' => $redis_p['host'],
        'port' => $redis_p['port'],
        'password' => $redis_p['pass'],
    ]);
}
