<?php

use Symfony\Component\HttpFoundation\Request;

/*
$app->get('/', function() use ($app) {
    return $app->json(['foo'=>'bar']);
});
*/

$app->post('/', function(Request $request) use ($app) {
    return $app->json($request->request->all());
    // $request->request->get('key', 'default');
});

$app->get('/debug', function() use ($app) {
    $maxMemory = $app['predis']->get('debug:maxmemory');
    return $app->json([
        'max_memory' => $maxMemory,
    ]);
});
