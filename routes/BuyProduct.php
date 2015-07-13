<?php

$app->post('/ReqBuyProduct', function($request, $response) {
	$req = $request->getParsedBody();
/*
{
	"userId":null.
	"authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
	"sysId":"VK",
	"msgId":"123",
	"extId":"123439103",

	"productId":"creditsPack01",
	"levelMode":"arcade"
}
*/

	$template = [
		'ReqBuyProduct_NotRedyYet'
	];

	return render($response, $template);
});
