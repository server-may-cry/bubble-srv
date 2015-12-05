<?php
require_once __DIR__ .'/global.php';
require_once ROOT.'vendor/autoload.php'; // Composer Autoloader
require_once ROOT.'src/db.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$ravenClient = new Raven_Client(getenv('RAVEN_URL'));

$app = new Application();
$app->error( function (Exception $exception, $code) use ($app, $ravenClient) {
    if($app['debug']) {
        throw $exception;
    } else {
        $ravenClient->captureException(
            $exception,
            [
                'extra' => [
                    'php_version' => phpversion(),
                ],
            ]
        );

        $data = [
            'error' => get_class($exception),
            'message' => str_replace('"', "'", $exception->getMessage()),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        if($code !== 404) {
            $code = 500;
        }
        return $app->json($data, $code);
    }
});

// Install error handlers and shutdown function to catch fatal errors
$error_handler = new Raven_ErrorHandler($ravenClient);
$error_handler->registerExceptionHandler();
// test $error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();

$app->get('/', ['\\Routes\\IndexRoute', 'get']);
$app->post('/', ['\\Routes\\IndexRoute', 'post']);
$app->get('/favicon.ico', ['\\Routes\\IndexRoute', 'favicon']);
$app->get('/debug', ['\\Routes\\IndexRoute', 'debug']);
$app->get('/exception', ['\\Routes\\IndexRoute', 'test_exception']);
$app->get('/loaderio-b1605c8654686a992bd3968349d85b8e/', ['\\Routes\\IndexRoute', 'loader']);
$app->get('/loaderio-a1605b7f59f37748149caae19249ff85/', ['\\Routes\\IndexRoute', 'loader2']);

$app->post('/ReqBuyProduct', ['\\Routes\\ReqBuyProductRoute', 'action']);
$app->post('/ReqEnter', ['\\Routes\\ReqEnterRoute', 'action']);
$app->post('/ReqReduceCredits', ['\\Routes\\ReqReduceCreditsRoute', 'action']);
$app->post('/ReqReduceTries', ['\\Routes\\ReqReduceTriesRoute', 'action']);
$app->post('/ReqSavePlayerProgress', ['\\Routes\\ReqSavePlayerProgressRoute', 'action']);
$app->post('/ReqUsersProgress', ['\\Routes\\ReqUsersProgressRoute', 'action']);
$app->post('/VkPay', ['\\Routes\\PayVkRoute', 'action']);
$app->get('/OkPay', ['\\Routes\\PayOkRoute', 'action']);

return $app;
