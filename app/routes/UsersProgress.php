<?php

$app->path('ReqUsersProgress', function($request) use ($app) {
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

	$templateMask = [
		'NotRedyYet'
	];

	// манипуляции с шаблоном ответа (подстановка значений)
	$template = new \MyTemplate($templateMask);


	return $template->render();
});