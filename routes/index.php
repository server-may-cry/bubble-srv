<?php

use Symfony\Component\HttpFoundation\Request;

$app->get('/', function() use ($app) {
    return $app->json(['foo'=>'bar']);
});

$app->post('/', function(Request $request) use ($app) {
    return $app->json($request->request->all());
});
