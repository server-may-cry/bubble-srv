<?php

$app->get('/', function($request, $response) {
    return render($response, ['foo'=>'bar']);
});

$app->get('/test', function($request, $response) {
    return render($response, ['foo'=>'bar']);
});

$app->post('/test', function($request, $response) {
	// var_dump( $request->getBody() );
	var_dump( $request->getContents() );
	$request->rewind();
	var_dump( $request->getSize() );
	$request->rewind();
	var_dump( $request->getBody() );
	die();
    return render($response, request($request));
});
