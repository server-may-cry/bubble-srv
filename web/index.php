<?php
date_default_timezone_set('UTC');
error_reporting(-1); // Display ALL errors
ini_set('display_errors', '1');
define('ROOT', dirname(__DIR__) . '/');
define('ROUTE_ROOT', ROOT . 'routes/');
require_once(ROOT . 'gameConfig.php'); // Game constants

if(!defined('ENV_NAME')) {
    define('ENV_NAME', getenv('ENV_NAME'));
}
// Composer Autoloader
require ROOT . 'vendor/autoload.php';

$c = new \Slim\Container();

// Custom 404 Error Page
$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $log = R::dispense('404log');
        $log->request = $request->getUri();
        $log->dateTime = time();
        $log->raw = $request->getBody();
        R::store($log);
        return render($response, 'Not Found' . $request->getUri(), 404);
    };
};

// Display exceptions with error and 500 status
$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $data = [
            'error' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        if(ENV_NAME === 'production') {
            $client = new \Raygun4php\RaygunClient(getenv('RAYGUN_APIKEY'));
            $client->SendException($exception);

            Rollbar::init(array('access_token' => getenv('ROLLBAR_ACCESS_TOKEN')));
            Rollbar::report_exception($exception);
        }
        return render($response, $data, 500);
    };
};

$app = new \Slim\App($c);

function request(Psr\Http\Message\RequestInterface $request) {
    // ?? $data = $request->getParsedBody(); must be ok
    var_dump( (string)$request->getBody() );
    $data = json_decode( (string)$request->getBody() );
    if(is_object($data))
        return $data;
    else
        return new stdClass;
}
function render(Psr\Http\Message\ResponseInterface $response, $data, $status = 200) {
    $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json; charset=utf-8')

        //->getBody()
        //->rewind()
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

// Require all paths/routes
$routes = scandir(ROUTE_ROOT);
foreach ($routes as $route) {
    if ( is_file(ROUTE_ROOT . $route) ) {
        require ROUTE_ROOT . $route;
    }
}
$app->run();
