<?php

$app->post('/test', function() use ($app) {
	$VK_SECRET = getenv('VK_SECRET');

	render( ['vk'=>substr($VK_SECRET, 0, 4)] );
});