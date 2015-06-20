<?php
$dbopts = parse_url(getenv('DATABASE_URL'));
var_dump($dbopts);
error_log('start lf');
$dbh = new PDO(
	'pgsql:dbname='.ltrim($dbopts["path"],'/') .
	';host='.$dbopts["host"] .
	';port='.$dbopts["port"] ,
	$dbopts["user"] ,
	$dbopts["pass"]
);
//$dbh = new PDO('mysql:host=localhost;dbname=bubble', 'bubble');
try{
	error_log('db exec');
	$count = $dbh->exec('update user set remaining_tries = 5 where remaining_tries < 5');
	var_dump($count);
} catch (Exception $e) {
	error_log('db exec exception');
	var_dump('exception :(');
	var_dump( $e->getMessage() );
}
