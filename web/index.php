<?php
date_default_timezone_set('UTC');
error_reporting(-1); // Display ALL errors
ini_set('display_errors', '1');
define('ROOT', dirname(__DIR__) . '/');
define('ROUTE_ROOT', ROOT . 'routes/');
require_once(ROOT . 'gameConfig.php'); // Game constants
if(file_exists(ROOT . 'web/secret.php')) {
    require_once(ROOT . 'web/secret.php'); // srv env, keys
}

if(!defined('ENV_NAME')) {
    define('ENV_NAME', getenv('ENV_NAME'));
}
// Composer Autoloader
require ROOT . 'vendor/autoload.php';

$app = new \Slim\Slim([
        'mode' => ENV_NAME,
        'debug' => false,
    ]);
function request(Psr\Http\Message\RequestInterface $request) {
    // ?? $data = $request->getParsedBody(); must be ok
    $data = json_decode( $request->getBody() );
    if(is_object($data))
        return $data;
    else
        return new stdClass;
}
function render(Psr\Http\Message\ResponseInterface $response, $data, $status = 200) {
    $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json; charset=utf-8')

        ->getBody()
        ->rewind()
        ->write(
            json_encode(
                $data, JSON_FORCE_OBJECT
            )
        )
    ;
    return $response;
}

// RedBeanPHP 4
// http://redbeanphp.com/
require ROOT . 'rb.php';
$dburl = getenv('DATABASE_URL');
if(strlen($dburl)>0) {
    $dbopts = parse_url(getenv('DATABASE_URL'));
    R::setup('pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"].';port='.$dbopts["port"], $dbopts["user"], $dbopts["pass"]);
} else {
    R::setup('mysql:host=localhost;dbname=bubble', 'bubble');
}
R::setAutoResolve( TRUE );

// Throw Exceptions for everything so we can see the errors
set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});
// Display exceptions with error and 500 status
$app->error(function(\Exception $e, $request, $response) {
    // TODO
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

    return render($response, $data, 500);
});

// Custom 404 Error Page
$app->notFound(function($request, $response) {
    // TODO
    $log = R::dispense('404log');
    $log->request = $request->getUri();
    $log->dateTime = time();
    $log->raw = json_encode( $request->getBody() );
    R::store($log);
    return render($response, 'Not Found' . $request->getUri());
});

// Require all paths/routes
$routes = scandir(ROUTE_ROOT);
foreach ($routes as $route) {
    if ( is_file(ROUTE_ROOT . $route) ) {
        require ROUTE_ROOT . $route;
    }
}
$app->run();
