<?php
require_once __DIR__ .'/global.php';
require_once ROOT.'vendor/autoload.php'; // Composer Autoloader
require_once ROOT.'src/db.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();
$app->error( function (Exception $exception, $code) use ($app) {
    if($app['debug']) {
        throw $exception;
    } else {
        $data = [
            'error' => get_class($exception),
            'message' => str_replace('"', "'", $exception->getMessage()),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        if(getenv('ENV_NAME') === 'production') {
            Rollbar::init(array(
                'access_token' => getenv('ROLLBAR_ACCESS_TOKEN'),
                'root' => '/app',
            ));
            Rollbar::report_exception($exception);
        }
        return $app->json($data, $code);
    }
});

$redis_exist = strlen(getenv('REDISCLOUD_URL'));
if ($redis_exist) {
    $app['predis'] = $redis;
}

$app->before(function (Request $request) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace( is_array($data) ? $data : [] );
});

$app->finish(function() use ($app) {
    if(!isset($app['predis'])) {
        return;
    }
    $memory = memory_get_peak_usage();
    $prevMemory = $app['predis']->get('debug:maxmemory');
    if($prevMemory < $memory) {
        $app['predis']->set('debug:maxmemory', $memory);
    }
});

// Require all paths/routes
$routes = scandir(ROUTE_ROOT);
foreach ($routes as $route) {
    if ( is_file(ROUTE_ROOT . $route) ) {
        require ROUTE_ROOT . $route;
    }
}

return $app;
