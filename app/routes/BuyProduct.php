<?php

$app->path('ReqBuyProduct', function($request) use ($app) {
/*
{
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"productId":"creditsPack01",
	"sysId":"VK",
	"msgId":"123",
	"extId":"123439103",
	"levelMode":"arcade",
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