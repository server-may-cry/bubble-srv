<?php
define('ROOT', dirname(__DIR__) . '/');
define('APP_ROOT', ROOT . 'app/');
define('ROUTE_ROOT', APP_ROOT . 'routes/');
require_once(ROOT . 'web/config.php'); // Nazim constants
include_once(ROOT . 'web/secret.php'); // srv env, keys

if(!defined('BULLET_ENV')) {
	define('BULLET_ENV', getenv('BULLET_ENV'));
}
// Composer Autoloader
$loader = require ROOT . 'vendor/autoload.php';
var_dump(BULLET_ENV);
$app = new \Slim\Slim([
		'mode' => BULLET_ENV,
	]);
function request() {
	global $app;
	$data = json_decode( $app->request->getBody() );
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
	error_log('response: '.json_encode((object)$data));
}

// RedBeanPHP 4
// The Power ORM
// http://redbeanphp.com/
require APP_ROOT . 'rb.php';
$dburl = getenv('DATABASE_URL');
if(strlen($dburl)>0) {
	R::setup($dburl);
} else {
	R::setup('mysql:host=localhost;dbname=bubble', 'bubble');
}
R::setAutoResolve( TRUE );

require APP_ROOT . 'common.php';

// Require all paths/routes
$routes = array_diff(scandir(ROUTE_ROOT), ['.','..']);
foreach ($routes as $route) {
	if ( is_file(ROUTE_ROOT . $route) ) {
		require ROUTE_ROOT . $route;
	}
}
$app->run();
