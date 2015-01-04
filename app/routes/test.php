<?php

$app->path('test', function($request) use ($app) {
	return $request->params(); // auto json_encode
});
