<?php
$dbopts = parse_url(getenv('DATABASE_URL'));
$dbh = new PDO(
	'pgsql:dbname='.ltrim($dbopts["path"],'/') .
	';host='.$dbopts["host"] .
	';port='.$dbopts["port"] ,
	$dbopts["user"] ,
	$dbopts["pass"]
);
//$dbh = new PDO('mysql:host=localhost;dbname=bubble', 'bubble');
$count = $dbh->exec('update user set remaining_tries = 5 where remaining_tries < 5');
var_dump($count);
