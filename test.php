<?php

$host = getenv('BULLET_HOSTNAME');
if($host === false)
	$host = 'http://b.bl';
define('HOST', $host);

$tests = array_diff(scandir(__DIR__ . '/test'), array('.','..'));

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
}	 
$time_start = getmicrotime(); 

function curl($url, $data) {
	$ch = curl_init(HOST.'/'.$url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$rawAnswer = curl_exec($ch);
	curl_close($ch);
	$answer = json_decode($rawAnswer);
	if(!is_object($answer))
		echo '"'.$url.'" json false' . PHP_EOL;
	return $answer;
}

foreach ($tests as $test) {
	if ( is_file(__DIR__ . '/test/' . $test) ) {
		require __DIR__ . '/test/' . $test;
	}
}

echo  $host.PHP_EOL;
$time_end = getmicrotime();$time = $time_end - $time_start; 
echo 'time: '.$time.PHP_EOL; 