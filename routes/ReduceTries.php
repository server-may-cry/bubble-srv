<?php

$app->post('/ReqReduceTries', function($request, $response) {
	$req = $request->getParsedBody();
/*
{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"sysId":"VK",
	"msgId":"123",
	"extId":"123439103",
	"userId":null
}
*/
	if(!isset($req->userId))
		throw new \Exception('user id not set');
	$user = R::findOne('users', 'id = ?', [(int)$req->userId]);

	if($user === NULL)
		throw new Exception("UserID: ".$req->userId.' not found');

	$user->remainingTries = max($user->remainingTries-1, 0);

	R::store($user);

	$template = [
		$user->remainingTries
	];

	return render($response, $template);
});
