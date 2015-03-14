<?php

$app->path('ReqReduceCredits', function($request) use ($app) {
/*
{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"amount":"10",
	"sysId":"VK",
	"msgId":"123",
	"extId":"123439103",
	"userId":null
}
*/
	if(!$request->userId)
		throw new \Exception('user id not set');
	$user = R::findOne('user', 'id = ?', [(int)$request->userId]);

	if($user === NULL)
		throw new Exception("UserID: ".$request->userId.' not found');

	$user->credits -= $request->amount;

	R::store($user);

	$template = [
		'reqMsgId' => $request->msgId,
		'userId' => $user->id,
		'credits' => $user->credits,
	];

	return $template;
});