<?php

$app->get('/', function($request, $response) {
    return render($response, ['foo'=>'bar']);
});

$app->get('/test', function($request, $response) {
    return render($response, ['foo'=>'bar']);
});

$app->post('/test', function($request, $response) {
	var_dump( $request->getBody() );
	die();
    return render($response, request($request));
});
