<?php
define('ROOT', dirname(__DIR__) . '/');
define('APP_ROOT', ROOT . 'app/');
//define('SRC_ROOT', APP_ROOT . 'src/');
define('ROUTE_ROOT', APP_ROOT . 'routes/');
define('TEMPLATES_ROOT', APP_ROOT . 'templates/');
define('DB_CONFIG_ROOT', APP_ROOT . 'db/');

// Composer Autoloader
$loader = require ROOT . 'vendor/autoload.php';

// RedBeanPHP 4
// The Power ORM
// http://redbeanphp.com/
require APP_ROOT . 'rb.php';
R::setup('mysql:host=localhost;dbname=bubble', 'bubble','');
//R::addDatabase( 'DB1', 'sqlite:/tmp/d1.db', 'usr', 'pss', $frozen );

// Bullet App
$app = new Bullet\App(require APP_ROOT . 'config.php');
$request = new Bullet\Request();

require APP_ROOT . 'common.php';

if( $request->isBot() ) {
	$log = R::dispense('botrequest');
	$log->dateTime = time();
	$log->raw = $request->raw();
	R::store($log);
	die();
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
