<?php

$ch = curl_init($host.'/enter');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/json"));
$data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1234","msgId":"123","referer":null}';
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$rawAnswer = curl_exec($ch);
$answer = json_decode($rawAnswer);
if(!is_object($answer)){
	echo 'test /enter json false' . PHP_EOL;
}
echo $answer->userId;
echo '. ';