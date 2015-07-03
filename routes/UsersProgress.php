<?php

$app->post('/ReqUsersProgress', function($request, $response) {
	$request = request($request);
/*
{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"sysId":"VK",
	"msgId":"123",
	"extId":"123439103",
	"socIds":[
		"soc0, soc1, soc2, soc3"
	],
	"userId":null
}
*/

	if(!isset($request->userId))
		throw new \Exception('user id not set');
	$user = R::findOne('users', 'id = ?', [(int)$request->userId]);

	if($user === NULL)
		throw new Exception("UserID: ".$request->userId.' not found');

	$friendsIds = explode(',',  str_replace(' ', '', $request->socIds[0]));
	$friends = R::find('users', 
		' ext_id IN ('.R::genSlots( $friendsIds ).')',
		$friendsIds);
	$template = [];

	foreach($friends as $friend) {
		if( $user->sys_id != $friend['sys_id'] )
			continue;
		$template[] = [
			'userId' => $friend['id'],
			'socId' =>  $friend['extId'],
			'reachedStage01' => $friend['reachedStage01'],
			'reachedStage02' => $friend['reachedStage02'],
			'reachedSubStage01' => $friend['reachedSubStage01'],
			'reachedSubStage02' => $friend['reachedSubStage02'],
		];
	}

	return render($response, $template);
});