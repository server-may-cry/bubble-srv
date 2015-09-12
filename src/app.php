<?php
require_once __DIR__ . '/global.php';
require_once ROOT . 'src/gameConfig.php'; // Game constants
require_once ROOT . 'vendor/autoload.php'; // Composer Autoloader

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineORMServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

$app = new Application();

// Register Doctrine DBAL
$dbOptions = [];
$dbURL = getenv('DATABASE_URL');
if($dbURL) {
    $dbopts = parse_url($dbURL);
    $dbOptions = [
        'driver' => 'pdo_pgsql',
        'host' => $dbopts["host"],
        'port' => $dbopts["port"],
        'dbname' => ltrim($dbopts["path"],'/'),
        'user' => $dbopts["user"],
        'password' => $dbopts["pass"],
        'charset' => 'utf8mb4',
    ];
} else {
    $dbOptions = [
        'driver' => 'pdo_sqlite',
        'path' => ROOT . '/tests/test.sqlite.db',
    ];
}
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => $dbOptions,
));

// Register Doctrine ORM
$app->register(new DoctrineORMServiceProvider(), [
    'db.orm.proxies_dir' => ROOT . '/cache/doctrine/proxy',
    'db.orm.proxies_namespace' => 'DoctrineProxy',
    'db.orm.cache' => 
        !$app['debug'] && extension_loaded('apc') ? new ApcCache() : new ArrayCache(),
    'db.orm.auto_generate_proxies' => true,
    'db.orm.entities' => [[
        'type' => 'annotation',       // entity definition 
        'path' => ROOT . '/src/entity',   // path to your entity classes
        'namespace' => 'Entity', // your classes namespace
    ]],
]);

$app->error( function (Exception $exception, $code) use ($app) {
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

    if($app['debug']) {
        throw $exception;
    } else {
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
