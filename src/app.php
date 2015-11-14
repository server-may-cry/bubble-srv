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
        if($code !== 404) {
            $code = 500;
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
    if(is_array($data)) {
        $request->request->replace($data);
    }
});

/*
$app->finish(function() use ($app) {
    if(!isset($app['predis'])) {
        return;
    }
    $memory = memory_get_peak_usage(true);
    $prevMemory = $app['predis']->get('debug:maxmemory');
    if($prevMemory < $memory) {
        $app['predis']->set('debug:maxmemory', $memory);
    }
});
*/

$app->get('/', ['\\Routes\\IndexRoute', 'get']);
$app->post('/', ['\\Routes\\IndexRoute', 'post']);
$app->get('/debug', ['\\Routes\\IndexRoute', 'debug']);

$app->post('/ReqBuyProduct', ['\\Routes\\ReqBuyProductRoute', 'post']);
$app->post('/ReqEnter', ['\\Routes\\ReqEnterRoute', 'post']);
$app->post('/ReqReduceCredits', ['\\Routes\\ReqReduceCreditsRoute', 'post']);
$app->post('/ReqReduceTries', ['\\Routes\\ReqReduceTriesRoute', 'post']);
$app->post('/ReqSavePlayerProgress', ['\\Routes\\ReqSavePlayerProgressRoute', 'post']);
$app->post('/ReqUsersProgress', ['\\Routes\\ReqUsersProgressRoute', 'post']);
$app->post('/VkPay', ['\\Routes\\VkPayRoute', 'post']);

return $app;
