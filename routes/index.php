<?php

$app->get('/', function($request, $response) {
    return render($response, ['foo'=>'bar']);
});

$app->get('/test', function($request, $response) {
    return render($response, ['foo'=>'bar']);
});

$app->post('/test', function($request, $response) {
    return render($response, request($request));
});
