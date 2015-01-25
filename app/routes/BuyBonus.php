<?php

$app->path('ReqBuyBonus', function($request) use ($app) {
/*
{
	"msgId":"123",
	"sysId":"VK",
	"userId":null,
	"extId":"123439103",
	"bonusId":"creditsPack01",
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c"
}
*/

	$templateMask = [
		'NotRedyYet'
	];

	// манипуляции с шаблоном ответа (подстановка значений)
	$template = new \MyTemplate($templateMask);


	return $template->render();
});