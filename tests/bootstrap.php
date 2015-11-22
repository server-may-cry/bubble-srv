<?php
require dirname(__DIR__) . '/src/global.php';

social\VK::setTestMode();

use Silex\WebTestCase;
use RedBeanPHP\Driver\RPDO as RPDO;

class TestBootstrap extends WebTestCase
{
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
        R::close();
        R::setup(); // SQLite in memory
        $pdo = RPDO::getPDO();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        RPDO::setPDO($pdo);

        return $app;
    }
}
