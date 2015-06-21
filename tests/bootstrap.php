<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Environment;


class RoutesTest /* extends PHPUnit_Framework_TestCase */
{
    protected $app;

    public function request($method, $path, $options = array())
    {
        // Capture STDOUT
        ob_start();

        // Prepare a mock environment
        Environment::mock(array_merge(array(
            'REQUEST_METHOD' => $method,
            'PATH_INFO' => $path,
            'SERVER_NAME' => 'slim-test.dev',
        ), $options));

        if(!$this->app) {
            $this->app = new \Slim\Slim([
                'debug' => false,
                'mode' => 'testing',
            ]);
            $routes = scandir(__DIR__ . '/../app/routes');
            foreach ($routes as $route) {
                if ( is_file(__DIR__ . '/../app/routes' . $route) ) {
                    require __DIR__ . '/../app/routes' . $route;
                }
            }
            $this->request = $this->app->request();
            $this->response = $this->app->response();
        }

        // Return STDOUT
        return ob_get_clean();
    }

    public function post($path, $options = array())
    {
        $this->request('POST', $path, $options);
    }

    public function get($path)
    {
        $this->request('GET', $path, []);
    }

    public function t_testIndex()
    {
        $this->get('/');
        $this->assertEquals('200', $this->response->status());
    }
}
