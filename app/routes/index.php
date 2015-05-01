<?php

$app->get('/', function() use ($app) {
	render( ['foo'=>'bar'] );
});