<?php

$app->path('enter', function($request) use ($app) {

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
		'subStagesRecordStats01'=>[[],[]], // array of array unsigned int
		'subStagesRecordStats02'=>[[],[]], // array of array unsigned int
	];

	// манипуляции с шаблоном ответа (подстановка значений)
	$template = new MyTemplate($templateMask);

	return $template->template();
});