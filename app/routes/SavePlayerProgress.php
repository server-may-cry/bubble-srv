<?php

$app->post('/ReqSavePlayerProgress', function() use ($app) {
	$request = request();
/*
{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"sysId":"VK",
	"msgId":"123",
	"extId":"123439103",
	"userId":null,

	"reachedSubStage":"1", // номер точки на острове до которой игрок дошел за все время игры
	"currentStage":"0", // номер острова на котором игрок прошел точку в текущей сессии
	"reachedStage":"0", // номер острова до которого игрок дошел за все время игры
	"completeSubStage":"1", // номер точки на острове которую игрок прошел в текущей сессии
	"completeSubStageRecordStat":"2", // количество звезд набранных на пройденной точке(перезаписывается только в случае если новое значение больше предыдущего)
	"levelMode":"standart", // режим игры, может принимать значения "standart" (01) и "arcade" (02)
	Если режим игры "standart", то перезаписываются значения reachedStage01 reachedSubStage01, 
	которые приходят в ReqEnter`e, если же "arcade" то reachedStage02 и reachedSubStage02
}
*/
	if(!$request->userId)
		throw new \Exception('user id not set');
	$user = R::findOne('user', 'id = ?', [$request->userId]);

	if($user === NULL)
		throw new Exception("UserID: ".$request->userId.' not found');

	switch($request->levelMode) {
		case 'standart':
			$user->reachedStage01 = max((int)$request->reachedStage, $user->reachedStage01);
			$user->reachedSubStage01 = max((int)$request->reachedSubStage, $user->reachedSubStage01);
			break;
		case 'arcade':
			$user->reachedStage02 = max((int)$request->reachedStage, $user->reachedStage02);
			$user->reachedSubStage02 = max((int)$request->reachedSubStage, $user->reachedSubStage02);
			break;
	}

	R::store($user);

	$star = R::findOne('star', 'user_id = ? AND level_mode = ? AND current_stage = ? AND complete_sub_stage = ?',
		[
			$user->id,
			$request->levelMode,
			(int)$request->currentStage,
			(int)$request->completeSubStage,
		]
	);

	if($star === NULL) {
		$star = R::dispense('star');
		$star->user = $user;
		$star->levelMode = $request->levelMode;
		$star->currentStage = (int)$request->currentStage;
		$star->completeSubStage = (int)$request->completeSubStage;
		$star->completeSubStageRecordStat = (int)$request->completeSubStageRecordStat;
		R::store($star);
	} elseif($star->completeSubStageRecordStat < $request->completeSubStageRecordStat) {
		$star->completeSubStageRecordStat = (int)$request->completeSubStageRecordStat;
		R::store($star);
	}

	// в этом запросе ответ не имеет значения
	render( 'ok' );
});