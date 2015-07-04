<?php

$app->get('/', function($request, $response) {
    return render($response, ['foo'=>'bar']);
});

$app->get('/test', function($request, $response) {
    return render($response, ['foo'=>'bar']);
});

$app->post('/test', function($request, $response) {
	// var_dump( $request->getBody() );
	$request->getBody()->rewind();
	var_dump( (string)$request->getBody() );
	var_dump( $request->getBody()->getContents() );
	$request->getBody()->rewind();
	var_dump( $request->getBody()->getSize() );
	$request->getBody()->rewind();
	var_dump( (string)$request->getBody() );
	die();
    return render($response, request($request));
});
