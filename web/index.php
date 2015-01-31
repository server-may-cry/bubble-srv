<?php
define('ROOT', dirname(__DIR__) . '/');
define('APP_ROOT', ROOT . 'app/');
define('SRC_ROOT', APP_ROOT . 'src/');
define('ROUTE_ROOT', APP_ROOT . 'routes/');
define('TEMPLATES_ROOT', APP_ROOT . 'templates/');

// Composer Autoloader
$loader = require ROOT . 'vendor/autoload.php';

// RedBeanPHP 4
// The Power ORM
// http://redbeanphp.com/
require APP_ROOT . 'rb.php';
//phpinfo();
try {
    new PDO('pgsql:host=localhost;dbname=bubble', 'bubble', 'bubble');
    R::setup('pgsql:host=localhost;dbname=bubble', 'bubble', 'bubble');
} catch (PDOException $e) {
    R::setup('mysql:host=localhost;dbname=bubble', 'bubble');
} 

// Bullet App
$app = new Bullet\App(require APP_ROOT . 'config.php');
$request = new Bullet\Request();

require APP_ROOT . 'common.php';

if( $request->isBot() ) {
	$log = R::dispense('botrequest');
	$log->dateTime = time();
	$log->url = $request->url();
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
