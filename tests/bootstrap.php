<?php
require dirname(__DIR__) . '/src/global.php';

social\VK::setTestMode();

use Silex\WebTestCase;
use RedBeanPHP\Driver\RPDO as RPDO;

class TestBootstrap extends WebTestCase
{
    public function tearDown()
    {
        parent::tearDown();
        R::close();
    }

    protected function post($url, array $parameters = [])
    {
        $client = $this->createClient();
        $client->request('POST', $url, [], [], [], json_encode($parameters) );

        $this->assertTrue($client->getResponse()->isOk(), "{$url} failed with data: ".var_export($parameters, true) );
        return json_decode( $client->getResponse()->getContent(), true );
    }

    public function createApplication()
    {
        $app = require ROOT . '/src/app.php';
        $app['debug'] = true;
        R::setup(); // SQLite in memory

        return $app;
    }
}
