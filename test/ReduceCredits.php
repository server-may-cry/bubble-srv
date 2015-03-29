<?php

$reschedLevel = 40;
$data = '{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"sysId":"test",
	"extId":"1234",
	"amount":"2",
	"msgId":"123",
	"userId":'.$userID.'
}';

$answer0 = curl('ReqEnter', $data);
$answer = curl('ReqReduceCredits', $data);
if($answer !== NULL){
	$answer2 = curl('ReqEnter', $data);
	if($answer2 !== null) {
		if($answer2->credits >= $answer0->credits)
			echo '"ReqEnter" credits not dicreased ('.$answer0->credits.')'.PHP_EOL;
		else
			echo '. ';
	} else {
		'reqenter after reduce credits problem';
	}
}
