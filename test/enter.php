<?php

$data = '{"isTest":true,"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1234","msgId":"123","referer":null}';
$answer = curl('ReqEnter', $data);
if(is_object($answer)){
	if(!$answer->userId)
		echo '"ReqEnter" registration fail'.PHP_EOL;
	$userID = $answer->userId; // next tests
	$answer2 = curl('ReqEnter', $data);
	if ($answer2->userId == $answer->userId) {
		echo '. ';
	} else {
		echo '"ReqEnter" duplicate user'.PHP_EOL;
	}
	$data2 = '{"isTest":true,"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"testooo","extId":"1234","msgId":"123","referer":null}';
	$answer3 = curl('ReqEnter', $data2);
	if($answer3->userId == $answer2->userId) {
		echo '"ReqEnter" wrong user'.PHP_EOL;
	} else
		echo '. ';
}
