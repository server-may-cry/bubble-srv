<?php
require_once __DIR__ . '/global.php';
require_once ROOT . 'src/gameConfig.php'; // Game constants
require_once ROOT . 'vendor/autoload.php'; // Composer Autoloader
require_once ROOT . 'rb.php'; // RedBeanPHP 4

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineORMServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

$app = new Application();

// http://redbeanphp.com/
require ROOT . 'rb.php';
$dburl = getenv('DATABASE_URL');
if(strlen($dburl)>0) {
    $dbopts = parse_url($dburl);
    R::setup('pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"].';port='.$dbopts["port"], $dbopts["user"], $dbopts["pass"]);
} else {
    R::setup(); // SQLite in memory
}
R::setAutoResolve( TRUE );

$app->error( function (Exception $exception, $code) use ($app) {
    if($app['debug']) {
        throw $exception;
    } else {
        $data = [
            'error' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        if(getenv('ENV_NAME') === 'production') {
            Rollbar::init(array('access_token' => getenv('ROLLBAR_ACCESS_TOKEN')));
            Rollbar::report_exception($exception);
        }
        return $app->json($data, $code);
    }
});

$app->before(function (Request $request) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace( is_array($data) ? $data : [] );
});

// Require all paths/routes
$routes = scandir(ROUTE_ROOT);
foreach ($routes as $route) {
    if ( is_file(ROUTE_ROOT . $route) ) {
        if($route != 'index.php')
            continue;
        require ROUTE_ROOT . $route;
    }
}

return $app;
