<?php
define('ROOT', dirname(__DIR__) . '/');
define('APP_ROOT', ROOT . 'app/');
define('ROUTE_ROOT', APP_ROOT . 'routes/');
require_once(ROOT . 'web/config.php'); // Nazim constants
include_once(ROOT . 'web/secret.php'); // srv env, keys

if(!defined('ENV_NAME')) {
	define('ENV_NAME', getenv('ENV_NAME'));
}
// Composer Autoloader
$loader = require ROOT . 'vendor/autoload.php';

$app = new \Slim\Slim([
		'http.version' => '1.1',
		'mode' => ENV_NAME,
		'debug' => false,
	]);
function request() {
	global $app;
	$data = json_decode( $app->request->getBody() );
	if(is_object($data))
		return $data;
	else
		return new stdClass;
}
function render($data, $status = 200) {
	global $app;
	$response = $app->response();
	$response['Content-Type'] = 'application/json; encoding=utf-8';
	$response->status($status);
	$response->body(
		json_encode(
			(object)$data
		)
	);
	//error_log('response: '.json_encode((object)$data));
}

// RedBeanPHP 4
// The Power ORM
// http://redbeanphp.com/
require APP_ROOT . 'rb.php';
$dburl = getenv('DATABASE_URL');
if(strlen($dburl)>0) {
	$dbopts = parse_url(getenv('DATABASE_URL'));
	R::setup('pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"].';port='.$dbopts["port"], $dbopts["user"], $dbopts["pass"]);
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
