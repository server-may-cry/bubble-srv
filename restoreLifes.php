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
try{
    var_dump('db exec');
    $count = $dbh->exec('update users set remaining_tries = 5 where remaining_tries < 5');
    error_log('db exec end');
    error_log('db exec count '.var_export($count, true) );
    var_dump($count);
} catch (Exception $e) {
    error_log('db exec exception');
    var_dump('exception :(');
    var_dump( $e->getMessage() );
}
