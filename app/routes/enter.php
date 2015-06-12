<?php

$app->post('/ReqEnter', function() use ($app) {
	$request = request();
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
	if(!isset($request->sysId))
		throw new \Exception('Social platform not set. request: '.json_encode($request));
	if(!isset($request->extId))
		throw new \Exception('Social id not set');
	$user = R::findOne('user', 'sys_id = ? AND ext_id = ?', [$request->sysId, (int)$request->extId ]);

	$bonusCredits = 0;
	$userFriendsBonusCredits = 0;
	$timestamp = time();
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
		$user->bonusCreditsReceiveTime = $timestamp;
		$user->friendsBonusCreditsTime = $timestamp;
		$user->id = R::store($user);
	}
	$needUpdateTimer = false;
	if( $timestamp - $user->bonusCreditsReceiveTime > intervalBonusCreditsReceiveTime ) {
		$user->bonusCreditsReceiveTime = $timestamp;
		$user->credits += bonusCreditsReceive;
		$bonusCredits = bonusCreditsReceive;
		$needUpdateTimer = true;
	}
	if( $timestamp - $user->friendsBonusCreditsTime > intervalFriendsBonusCreditsReceiveTime) {
		$user->friendsBonusCreditsTime = $timestamp;
		$userFriendsBonusCredits = $request->appFriends * userFriendsBonusCreditsMultiplier;
		$user->credits += $userFriendsBonusCredits;
		$needUpdateTimer = true;
	}
	if($needUpdateTimer)
		R::store($user);

	$islandsLevelCount = [
		array_fill(0,ISLAND_1_COUNT,-1),
		array_fill(0,ISLAND_2_COUNT,-1),
		array_fill(0,ISLAND_3_COUNT,-1),
		array_fill(0,ISLAND_4_COUNT,-1),
		array_fill(0,ISLAND_5_COUNT,-1),
		array_fill(0,ISLAND_6_COUNT,-1),
	];

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
		'bonusCredits'=>$bonusCredits, // Количество монет в 12-часовом бонусе, сейчас это 1000(возможно надо будет куда-нибудь вынести как параметр)
		'appFriendsBonusCredits'=>$userFriendsBonusCredits, // Количество монет в ежедневном бонусе за друзей. Рассчитывается по формуле reqEnter.appFriends умноженное на 30 монет за друга
		'offerAvailable'=>0, // Может принимать значения 0 и 1. Включать ли акцию сегодня или нет(возможно надо будет куда-нибудь вынести как параметр)
		'firstGame'=>$firstGame, // Может принимать значения 0 и 1. Если пользователь зашел в игру в первый раз в жизни, то 1, в остальных случаях 0.
		'stagesProgressStat01'=>[], // unsigned integer array // Список чисел. Каждое число обозначает количество игроков дошедших до определенного уровня в стандартном моде. // острова
		'stagesProgressStat02'=>[], // Список объектов subStagesRecordStat. Отображает количество звезд на подуровнях в стандартном моде.
		
		// индекс первого массива это reachedStage, а во втором массиве это reachedSubStage, а самое значение в массиве это reqSavePlayerProgress.completeSubStageRecordStat
		'subStagesRecordStats01'=>$islandsLevelCount,
		'subStagesRecordStats02'=>$islandsLevelCount,
	];

	$collectionStars = R::findCollection('star', 'user_id = 1');
	while( $star = $collectionStars->next() ) {
        switch( $star->levelMode ) {
        	case 'standart':
        		$key = 'subStagesRecordStats01';
        		break;
    		case 'standart':
        		$key = 'subStagesRecordStats02';
        		break;
    		default:
    			throw new Exception('Unknown game type in DB: ' . $star->levelMode);
        }
        $template [ $key ] [ $star->currentStage ] [ $star->completeSubStage ] = $star->completeSubStageRecordStat;
    }

    $usersProgresStandartRaw = R::getAll('select count(*) as `count`, reached_stage01 from bubble.user group by reached_stage01 order by reached_stage01 desc;');
    $usersProgresStandart = [];
    $i = 0;
    $playersCount = 0;
    foreach($usersProgresStandartRaw as $row) {
    	$playersCount += $row['count'];
    	while($i++ < $row['reached_stage01']) {
    		$usersProgresStandart[] = $playersCount;
    	}
    }
    $template['stagesProgressStat01'] = $usersProgresStandart;

    $usersProgresArcadeRaw = R::getAll('select count(*) as `count`, reached_stage02 from bubble.user
     group by reached_stage02 order by reached_stage02 desc;');
    $usersProgresArcade = [];
    $i = 0;
    $playersCount = 0;
    foreach($usersProgresArcadeRaw as $row) {
    	$playersCount += $row['count'];
    	while($i++ < $row['reached_stage02']) {
    		$usersProgresArcade[] = $playersCount;
    	}
    }
    $template['stagesProgressStat02'] = $usersProgresArcade;
	
	render( $template );
});
