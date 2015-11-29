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

$app->before(function (Request $request) {
    $data = json_decode($request->getContent(), true);
    if(is_array($data)) {
        $request->request->replace($data);
    }
});

$app->get('/', ['\\Routes\\IndexRoute', 'get']);
$app->post('/', ['\\Routes\\IndexRoute', 'post']);
$app->get('/debug', ['\\Routes\\IndexRoute', 'debug']);
$app->get('/loaderio-b1605c8654686a992bd3968349d85b8e/', ['\\Routes\\IndexRoute', 'loader']);

$app->post('/ReqBuyProduct', ['\\Routes\\ReqBuyProductRoute', 'action']);
$app->post('/ReqEnter', ['\\Routes\\ReqEnterRoute', 'action']);
$app->post('/ReqReduceCredits', ['\\Routes\\ReqReduceCreditsRoute', 'action']);
$app->post('/ReqReduceTries', ['\\Routes\\ReqReduceTriesRoute', 'action']);
$app->post('/ReqSavePlayerProgress', ['\\Routes\\ReqSavePlayerProgressRoute', 'action']);
$app->post('/ReqUsersProgress', ['\\Routes\\ReqUsersProgressRoute', 'action']);
$app->post('/VkPay', ['\\Routes\\PayVkRoute', 'action']);
$app->get('/OkPay', ['\\Routes\\PayOkRoute', 'action']);

return $app;
