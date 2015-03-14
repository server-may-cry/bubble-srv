<?php
define('ROOT', dirname(__DIR__) . '/');
define('APP_ROOT', ROOT . 'app/');
define('ROUTE_ROOT', APP_ROOT . 'routes/');
define('TEMPLATES_ROOT', APP_ROOT . 'templates/');
include_once(ROOT . 'web/config.php'); // Nazim constants

// Composer Autoloader
$loader = require ROOT . 'vendor/autoload.php';

// Bullet App
$app = new Bullet\App(require APP_ROOT . 'config.php');
$request = new Bullet\Request();

// RedBeanPHP 4
// The Power ORM
// http://redbeanphp.com/
require APP_ROOT . 'rb.php';
if($request->isTest) {
	R::setup('sqlite:'.ROOT.'/web/test.db'); // SQLite DB in temp dir
} else {
	R::setup('mysql:host=localhost;dbname=bubble', 'bubble');
}
R::setAutoResolve( TRUE );
R::freeze( READ_BEAN_FREEZE );

require APP_ROOT . 'common.php';

if( $request->isBot() ) {
	$log = R::dispense('botrequest');
	$log->dateTime = time();
	$log->url = $request->url();
	R::store($log);
	die('API service');
}

// Require all paths/routes
$routes = array_diff(scandir(ROUTE_ROOT), array('.','..'));
foreach ($routes as $route) {
	if ( is_file(ROUTE_ROOT . $route) ) {
		require ROUTE_ROOT . $route;
	}
}

// Response
echo $app->run( $request );
