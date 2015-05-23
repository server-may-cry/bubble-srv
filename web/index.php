<?php
define('ROOT', dirname(__DIR__) . '/');
define('APP_ROOT', ROOT . 'app/');
define('ROUTE_ROOT', APP_ROOT . 'routes/');
include_once(ROOT . 'web/config.php'); // Nazim constants

// Composer Autoloader
$loader = require ROOT . 'vendor/autoload.php';

$app = new \Slim\Slim([
		'mode' => BULLET_ENV,
	]);
$data = json_decode( $app->request->getBody() );
function request() {
	global $data;
	if(is_object($data))
		return $data;
	else
		return new stdClass;
}
function render($data) {
	global $app;
	$response = $app->response();
	$response['Content-Type'] = 'application/json; encoding=utf-8';
	$response->status(200);
	$response->body(
		json_encode(
			(object)$data
		)
	);
}

// RedBeanPHP 4
// The Power ORM
// http://redbeanphp.com/
require APP_ROOT . 'rb.php';
if(request()->isTest) {
	R::setup('sqlite:'.ROOT.'/web/test.db'); // SQLite DB in temp dir
} else {
	R::setup('mysql:host=localhost;dbname=bubble', 'bubble');
}
R::setAutoResolve( TRUE );

require APP_ROOT . 'common.php';

// Require all paths/routes
$routes = array_diff(scandir(ROUTE_ROOT), array('.','..'));
foreach ($routes as $route) {
	if ( is_file(ROUTE_ROOT . $route) ) {
		require ROUTE_ROOT . $route;
	}
}

$app->run();
