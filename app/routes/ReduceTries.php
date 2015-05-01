<?php

$app->post('/ReqReduceTries', function() use ($app) {
	$request = request();
/*
{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
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

	$user->remainingTries = max($user->remainingTries-1, 0);

	R::store($user);

	$template = [
		$user->remainingTries
	];

	render( $template );
});