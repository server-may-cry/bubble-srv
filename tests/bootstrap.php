<?php
require dirname(__DIR__) . '/src/global.php';

$dburl = getenv('DATABASE_URL');
if(strlen($dburl)>0) {
    die('never run unit test on production couse data los');
}

social\VK::setTestMode();

use Silex\WebTestCase;
R::setup(); // SQLite in memory

class TestBootstrap extends WebTestCase
{
    public function tearDown()
    {
        // drop db
        R::nuke();
        $app = $this->createApplication();
        if(isset($app['predis'])) {
            $app['predis']->flush();
        }
        parent::tearDown();
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

        return $app;
    }
}
