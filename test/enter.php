<?php

$ch = curl_init($host.'/enter');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/json"));
$data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1234","msgId":"123","referer":null}';
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$rawAnswer = curl_exec($ch);
curl_close($ch);
$answer = json_decode($rawAnswer);
if(!is_object($answer)){
	echo '"/enter" json false' . PHP_EOL;
} else {
	$ch = curl_init($host.'/enter');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/json"));
	$data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1234","msgId":"123","referer":null}';
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$rawAnswer = curl_exec($ch);
	curl_close($ch);
	$answer2 = json_decode($rawAnswer);
	if(!is_object($answer2)){
		echo '"/enter" second json false' . PHP_EOL;
	} elseif ($answer2->userId == $answer->userId) {
		echo '. ';
	} else {
		echo '"/enter" duplicate user'.PHP_EOL;
	}
}