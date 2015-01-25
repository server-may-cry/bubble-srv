<?php

$app->path('ReqEnter', function($request) use ($app) {
/*
{
	"userId":null, // Идентификатор пользователя, получается с сервера приложения при входе в систему
	"appFriends":"0", // количество друзей в приложении
	"srcExtId":null, // vk_id друга откуда пришли
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"sysId":"VK",
	"extId":"123439103", // vk_id
	"msgId":"123", // unic ?
	"referer":null
}
*/
	if(!$request->sysId)
		throw new Exception('Social platform not set');
	if(!$request->extId)
		throw new Exception('Social id not set');
	$user = R::findOne('user', 'sys_id = ? AND ext_id = ?', [$request->sysId, (int)$request->extId ]);
	//var_dump($user);
	//var_dump($request->sysId, $request->extId);
	//die();
	if($user === NULL) {
		$user = R::dispense('user');
		$user->sysId = $request->sysId;
		$user->extId = $request->extId;
		$user->authKey = $request->authKey;
		$user->referer = $request->referer;
		$user->srcExtId = $request->srcExtId;
		$user->appFriends = $request->appFriends;
		$user->reachedStage01 = 1;
		$user->reachedSubStage01 = 1;
		$user->reachedStage02 = 1;
		$user->reachedSubStage02 = 1;
		$user->id = R::store($user);
	}

	$templateMask = [
		'reqMsgId'=>'',
		'userId'=>'',
		'reachedStage01'=>1,
		'reachedStage02'=>1,
		'reachedSubStage01'=>1,
		'reachedSubStage02'=>1,
		'ignoreSavePointBlock'=>1, //  Может принимать значения 0 и 1
		'remainingTries'=>5,
		'credits'=>0,
		'inifinityExtra00'=>0, // Целое положительное число
		'inifinityExtra01'=>0, // Целое положительное число
		'inifinityExtra02'=>0, // Целое положительное число
		'inifinityExtra03'=>0, // Целое положительное число
		'inifinityExtra04'=>0, // Целое положительное число
		'inifinityExtra05'=>0, // Целое положительное число
		'inifinityExtra06'=>0, // Целое положительное число
		'inifinityExtra07'=>0, // Может принимать значения 0 и 1
		'inifinityExtra08'=>0, // Может принимать значения 0 и 1
		'inifinityExtra09'=>0, // Может принимать значения 0 и 1
		'bonusCredits'=>0, // Количество монет в 12-часовом бонусе, сейчас это 1000(возможно надо будет куда-нибудь вынести как параметр)
		'appFriendsBonusCredits'=>0, // Количество монет в ежедневном бонусе за друзей. Рассчитывается по формуле reqEnter.appFriends умноженное на 30 монет за друга(возможно надо будет куда-нибудь вынести как параметр)
		'offerAvailable'=>0, // Может принимать значения 0 и 1. Включать ли акцию сегодня или нет(возможно надо будет куда-нибудь вынести как параметр)
		'firstGame'=>0, // Может принимать значения 0 и 1. Если пользователь зашел в игру в первый раз в жизни, то 1, в остальных случаях 0.
		'stagesProgressStat01'=>[], // unsigned integer array // Список чисел. Каждое число обозначает количество игроков дошедших до определенного уровня в стандартном моде.
		'stagesProgressStat02'=>[],
		// Список объектов subStagesRecordStat. Отображает количество звезд на подуровнях в стандартном моде.
		'subStagesRecordStats01'=>[[0,1,2],[3,2,1,0]], // array of array unsigned int
		'subStagesRecordStats02'=>[[0,1,2],[3,2,1,0]], // array of array unsigned int
	];

	// манипуляции с шаблоном ответа (подстановка значений)
	$template = new \MyTemplate($templateMask);

	$template->set('userId', $user->id);
	$template->set('credits', (int)$user->credits);
	$template->set('reachedStage01', $user->reachedStage01);
	$template->set('reachedStage02', $user->reachedStage02);
	$template->set('reachedSubStage01', $user->reachedSubStage01);
	$template->set('reachedSubStage02', $user->reachedSubStage02);

	return $template->render();
});