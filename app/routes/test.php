<?php

$app->get('/test', function() use ($app) {
	$VK_SECRET = getenv('VK_SECRET');
	$BULLET_ENV = getenv('BULLET_ENV');
	error_log(
			json_encode(
					[
						'vk'=>$VK_SECRET,
						'BULLET_ENV'=>$BULLET_ENV,
						'env'=>$_ENV,
					]
				)
		);
	render( 'see error log' );
});
