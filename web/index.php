<?php
date_default_timezone_set('UTC');
error_reporting(-1); // Display ALL errors
ini_set('display_errors', '1');
define('ROOT', dirname(__DIR__) . '/');
define('APP_ROOT', ROOT . 'app/');
define('ROUTE_ROOT', APP_ROOT . 'routes/');
require_once(ROOT . 'web/config.php'); // Nazim constants
if(file_exists(ROOT . 'web/secret.php')) {
    require_once(ROOT . 'web/secret.php'); // srv env, keys
}

if(!defined('ENV_NAME')) {
    define('ENV_NAME', getenv('ENV_NAME'));
}
// Composer Autoloader
$loader = require ROOT . 'vendor/autoload.php';

$app = new \Slim\Slim([
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
}

// RedBeanPHP 4
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

// Throw Exceptions for everything so we can see the errors
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");
// Display exceptions with error and 500 status
$app->error(function(\Exception $e) use($app) {
    $data = [
        'error' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ];
    if(ENV_NAME === 'production') {
        $client = new \Raygun4php\RaygunClient(getenv('RAYGUN_APIKEY'));
        $client->SendException($e);

        Rollbar::init(array('access_token' => getenv('ROLLBAR_ACCESS_TOKEN')));
        Rollbar::report_exception($e);
    }

    render( $data, 500 );
});

// Custom 404 Error Page
$app->notFound(function() use($app) {
    $log = R::dispense('404log');
    $log->request = $app->request->getResourceUri();
    $log->dateTime = time();
    $log->raw = json_encode( request() );
    R::store($log);
    render( 'Not Found' . $app->request->getResourceUri() );
});

// Require all paths/routes
$routes = scandir(ROUTE_ROOT);
foreach ($routes as $route) {
    if ( is_file(ROUTE_ROOT . $route) ) {
        require ROUTE_ROOT . $route;
    }
}
$app->run();
