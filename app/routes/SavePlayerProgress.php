<?php

$app->path('ReqSavePlayerProgress', function($request) use ($app) {
/*
{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"sysId":"VK",
	"msgId":"123",
	"extId":"123439103",
	"reachedSubStage":"1", // номер точки на острове до которой игрок дошел за все время игры
	"currentStage":"0", // номер острова на котором игрок прошел точку в текущей сессии
	"reachedStage":"0", // номер острова до которого игрок дошел за все время игры
	"completeSubStage":"1", // номер точки на острове которую игрок прошел в текущей сессии
	"completeSubStageRecordStat":"2", // количество звезд набранных на пройденной точке(перезаписывается только в случае если новое значение больше предыдущего)
	"levelMode":"standart", // режим игры, может принимать значения "standart" (01) и "arcade" (02)
	Если режим игры "standart", то перезаписываются значения reachedStage01 reachedSubStage01, 
	которые приходят в ReqEnter`e, если же "arcade", то reachedStage02 и reachedSubStage02
	"userId":null
}
// (все параметры выше - целые числа принимающие значения от 0)

	// currentStage
	// completeSubStage
	// completeSubStageRecordStat
	// userId
*/
	if(!$request->sysId)
		throw new Exception('Social platform not set');
	if(!$request->extId)
		throw new Exception('Social id not set');
	$user = R::findOne('user', 'sys_id = ? AND ext_id = ?', [$request->sysId, (int)$request->extId ]);

	if($user === NULL)
		throw new Exception("User ".$request->sysId.': '.$request->extId.' not found');
		
	switch($request->levelMode) {
		case 'standart':
			$user->reachedStage01 = max($reuest->reachedStage, $user->reachedStage01);
			$user->reachedSubStage01 = max($reuest->reachedSubStage, $user->reachedSubStage01);
			break;
		case 'arcade':
			$user->reachedStage02 = max($reuest->reachedStage, $user->reachedStage02);
			$user->reachedSubStage02 = max($reuest->reachedSubStage, $user->reachedSubStage02);
			break;
	}

	R::store($user);

	$templateMask = [
		'NotRedyYet'
	];

	// манипуляции с шаблоном ответа (подстановка значений)
	$template = new \MyTemplate($templateMask);


	return $template->render();
});