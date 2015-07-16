<?php
require dirname(__DIR__) . '/bootstrap.php';

use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Uri;
use Slim\Http\Headers;
use Slim\Http\Body;

class SlimTest
{
    private static $app;
    private static $container;

    public static function post($route, array $data)
    {
        $mock = Environment::mock([
            "SCRIPT_NAME" => "/index.php",
            "REQUEST_URI" => $route,
            'REQUEST_METHOD' => 'POST',
            "HTTP_CONTENT_TYPE" => 'application/json',
        ]);
        self::$container['environment'] = $mock;

        $body = json_encode($data);
        $stream = fopen('data://text/plain,' . $body,'r');
        $request = new Request(
            'POST',
            new Uri('http', 'localhost', 80, $route),
            Headers::createFromEnvironment($mock),
            [], // cookies
            [], // serverParams
            new Body($stream)
        );
        self::$container['request'] = $request;

        ob_start();
        self::$app->run();
        $content = ob_get_clean();
        return $content;
        return json_decode($content);
    }

    public static function init(App $app)
    {
        self::$app = $app;
        self::$container = $app->getContainer();
    }
}
SlimTest::init($app);

$c = SlimTest::post('/ReqEnter', ['foo'=>'barz']);
var_dump($c);
$c = SlimTest::post('/ReqEnter', ['foo'=>'barz']);
var_dump($c);
