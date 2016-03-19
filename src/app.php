<?php
require_once dirname(__DIR__).'/vendor/autoload.php'; // Composer Autoloader
require_once __DIR__ .'/global.php';
require_once ROOT.'src/db.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$ravenClient = new Raven_Client(getenv('RAVEN_URL'));

$app = new Application();
$app->error(
    function (Exception $exception, $code) use ($app, $ravenClient) {
        if($app['debug']) {
            throw $exception;
        }

        $eventID = $client->getIdent(
            $ravenClient->captureException(
                $exception,
                [
                    'extra' => [
                        'php_version' => phpversion(),
                    ],
                ]
            )
        );

        $data = [
            'error' => get_class($exception),
            'id' => $eventID,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        if($code !== 404) {
            $code = 500;
        }
        return $app->json($data, $code);
    }
);

$app->register(new \Saxulum\Console\Provider\ConsoleProvider());
$app['console.command.paths'] = $app->extend(
    'console.command.paths', function ($paths) {
        $paths[] = ROOT.'src/Commands';

        return $paths;
    }
);

$error_handler = new Raven_ErrorHandler($ravenClient);
$error_handler->registerExceptionHandler();
$error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();
$app['raven'] = $error_handler;

$app->post('/ReqEnter', ['\\Routes\\ReqEnterRoute', 'action']);
$app->post('/ReqReduceTries', ['\\Routes\\ReqReduceTriesRoute', 'action']);
$app->post('/ReqSavePlayerProgress', ['\\Routes\\ReqSavePlayerProgressRoute', 'action']);
$app->post('/ReqReduceCredits', ['\\Routes\\ReqReduceCreditsRoute', 'action']);
$app->post('/ReqBuyProduct', ['\\Routes\\ReqBuyProductRoute', 'action']);
$app->post('/ReqUsersProgress', ['\\Routes\\ReqUsersProgressRoute', 'action']);
$app->post('/VkPay', ['\\Routes\\PayVkRoute', 'action']);
$app->get('/OkPay', ['\\Routes\\PayOkRoute', 'action']);
$app->match('/pay/{platform}', ['\\Routes\\PayRoute', 'action'])->assert('platform', 'vk|ok');

$app->match('/bubble/{any}', ['\\Routes\\StaticFiles', 'action'])->assert('any', '.+');
$app->get('/cache-clear', ['\\Routes\\StaticFiles', 'clear']);

$app->get('/', ['\\Routes\\IndexRoute', 'get']);
$app->post('/', ['\\Routes\\IndexRoute', 'post']);
$app->get('/favicon.ico', ['\\Routes\\IndexRoute', 'favicon']);
$app->get('/debug', ['\\Routes\\IndexRoute', 'debug']);
$app->get('/exception', ['\\Routes\\IndexRoute', 'test_exception']);
$app->get('/loaderio-42a36845d21f907d9077524bb26f9a9d/', ['\\Routes\\IndexRoute', 'loader']);

return $app;
