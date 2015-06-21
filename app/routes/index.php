<?php

$app->get('/', function() use ($app) {
    render( ['foo'=>'bar'] );
});

$app->get('/test', function() use ($app) {
    render( ['foo'=>'bar'] );
});
