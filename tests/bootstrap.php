<?php
require dirname(__DIR__) . '/src/global.php';

use Silex\WebTestCase;

class TestBootstrap extends WebTestCase
{

    public function setUp()
    {
        // create db scheme
        parent::setUp();
    }

    public function tearDown()
    {
        // drop db
        R::nuke();
        parent::tearDown();
    }

    protected function post($url, array $parameters)
    {
        $client = $this->createClient();
        $client->request('POST', $url, [], [], [], json_encode($parameters) );

        $this->assertTrue($client->getResponse()->isOk());
        return json_decode( $client->getResponse()->getContent(), true );
    }

    public function createApplication()
    {
        $app = require ROOT . '/src/app.php';
        $app['debug'] = true;

        return $app;
    }
}
