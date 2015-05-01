<?php

$app->post('/ReqUsersProgress', function() use ($app) {
	$request = request();
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

	if(!$request->userId)
		throw new \Exception('user id not set');
	$user = R::findOne('user', 'id = ?', [(int)$request->userId]);

	if($user === NULL)
		throw new Exception("UserID: ".$request->userId.' not found');

	$friends = R::find('user', 
		' ext_id IN ('.R::genSlots( $request->socIds ).') AND sys_id = ?',
		[ $request->socIds, $user->sys_id ]);
	$template = [];

	foreach($friends as $friend) {
		$template[] = [
			'userId' => $friend['id'],
			'socId' =>  $friend['extId'],
			'reachedStage01' => $friend['reachedStage01'],
			'reachedStage02' => $friend['reachedStage02'],
			'reachedSubStage01' => $friend['reachedSubStage01'],
			'reachedSubStage02' => $friend['reachedSubStage02'],
		];
	}

	render( $template );
});