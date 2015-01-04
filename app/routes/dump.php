<?php

$app->path('dump', function($request) use ($app) {
	$log = R::dispense('dumper');
	$log->dateTime = time();
	$log->requestData = str_replace("\n", ' _N_N_ ', var_export($request, true));
	//var_dump($log->requestData);
	$log->format = $request->format();
	$log->raw = $request->raw();
	$log->serialize = serialize($request);
	R::store($log);
    return ['dumped']; // auto json_encode
});