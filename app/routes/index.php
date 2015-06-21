<?php

$app->get('/', function() use ($app) {
    render( ['foo'=>'bar'] );
});

$app->get('/test', function() use ($app) {
	throw new Exception('test exception 4 sentry');
    render( ['foo'=>'bar'] );
});
