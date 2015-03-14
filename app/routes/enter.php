<?php

$app->path('ReqEnter', function(\Bullet\Request $request) use ($app) {
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
		throw new \Exception('Social platform not set');
	if(!$request->extId)
		throw new \Exception('Social id not set');
	$user = R::findOne('user', 'sys_id = ? AND ext_id = ?', [$request->sysId, (int)$request->extId ]);

	$firstGame = 0;
	if($user === NULL) {
		$firstGame = 1;
		$user = R::dispense('user');
		$user->sysId = $request->sysId;
		$user->extId = $request->extId;
		$user->authKey = $request->authKey;
		$user->referer = $request->referer;
		$user->srcExtId = $request->srcExtId;
		$user->appFriends = $request->appFriends;
		$user->reachedStage01 = 0;
		$user->reachedSubStage01 = 0;
		$user->reachedStage02 = 0;
		$user->reachedSubStage02 = 0;
		$user->remainingTries = defaultUserRemainingTries;
		$user->credits = defaultUserCredits;
		$user->id = R::store($user);
	}

	$template = [
		'reqMsgId'=>$request->msgId,
		'userId'=>$user->id,
		'reachedStage01'=>$user->reachedStage01, // Идентификатор уровня, до которого пользователь доиграл за все время игры в стандартном моде
		'reachedStage02'=>$user->reachedStage02, // Идентификатор подуровня, до которого пользователь доиграл за все время игры в стандартном моде
		'reachedSubStage01'=>$user->reachedSubStage01, // Идентификатор уровня, до которого пользователь доиграл за все время игры в аркадном моде
		'reachedSubStage02'=>$user->reachedSubStage02, // Идентификатор подуровня, до которого пользователь доиграл за все время игры в аркадном моде
		'ignoreSavePointBlock'=>1, //  Может принимать значения 0 и 1
		'remainingTries'=>$user->remainingTries,
		'credits'=>$user->credits,
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
		'firstGame'=>$firstGame, // Может принимать значения 0 и 1. Если пользователь зашел в игру в первый раз в жизни, то 1, в остальных случаях 0.
		'stagesProgressStat01'=>[], // unsigned integer array // Список чисел. Каждое число обозначает количество игроков дошедших до определенного уровня в стандартном моде. // острова
		'stagesProgressStat02'=>[], // Список объектов subStagesRecordStat. Отображает количество звезд на подуровнях в стандартном моде.
		'subStagesRecordStats01'=>[[0,1,2],[3,2,1,0]], // индекс первого массива это reachedStage, а во втором массиве это reachedSubStage, а самое значение в массиве это reqSavePlayerProgress.completeSubStageRecordStat
		'subStagesRecordStats02'=>[[0,1,2],[3,2,1,0]], // array of array unsigned int
	];

	// количество игроков дошедших до каждого острова
	/*$usersProgresRaw = R::getAll('select 
		count(*) as `cnt`, reached_stage01, reached_stage02
	from
		bubble.user
	where
		reached_stage01 is not null
	group by
		reached_stage01, reached_stage02
	order by
		reached_stage01, reached_stage02');
	$usersProgres = [];
	$i = 0;
	$totalUsers01 = 0;
	$totalUsers02 = 0;
	foreach($usersProgresRaw as $row) {
		$row->cnt
		$totalUsers01 += $row->reached_stage01;
		$totalUsers02 += $row->reached_stage02;
		++$i;
	}*/

	return $template;
});