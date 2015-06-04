<?php

$app->get('/test', function() use ($app) {
	$VK_SECRET = getenv('VK_SECRET');
	$BULLET_ENV = getenv('BULLET_ENV');
	render( ['vk'=>substr($VK_SECRET, 0, 4), 'BULLET_ENV'=>$BULLET_ENV] );
});