<?php

$reschedLevel = 40;
$data = '{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"sysId":"test",
	"msgId":"123",
	"extId":"1234",
	"reachedSubStage":"10",
	"currentStage":"20",
	"reachedStage":"'.$reschedLevel.'",
	"completeSubStage":"40",
	"completeSubStageRecordStat":"50",
	"levelMode":"standart",
	"userId":1
}';

$answer = curl('ReqSavePlayerProgress', $data);
if($answer !== NULL){
	$answer2 = curl('ReqEnter', $data);
	if($answer2->reachedStage01 != $reschedLevel)
		echo '"ReqSavePlayerProgress" progres not updated'.PHP_EOL;

}
