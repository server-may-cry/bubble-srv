<?php

namespace entity;

use Silex\Application;
use config\UserParams;

final class User
{

    const REDIS_KEY = 'u:%s:%d';

    const STRUCT = [
        'id' => '0',
        'tries' => (string) UserParams::$defaultUserRemainingTries,
        'credits' => (string) UserParams::$defaultUserCredits,
        'friendsBonusCreditsTime' => (string) time(),
        'flags' => '0',
    ];
    private $firstEnter = 0;
    private $pk = 'u::'; // save string, user primary key
    private $data = [];

    public function __construct(Application $app, $platform, $id)
    {
        $platform = 'VK';
        $id = (int)$id;
        $this->pk = sprintf(self::REDIS_KEY, $platform, $id);
        $this->data = $this->find();
    }

    public function getIsFirstEnter()
    {
        return $this->firstEnter;
    }

    public function addLifes($count)
    {

    }

    public function addCoins($count)
    {

    }

    public function buyGood($name)
    {

    }

    public function saveProgress()
    {

    }

    public function getStructure()
    {

    }

    private function find()
    {
        // active user
        $user = $this->findInRedis();
        if(count($user)) {
            return $user;
        }

        // inactive user
        $user = $this->findInPG();
        if(count($user)) {
            return json_decode($user);
        }

        // new user
        $this->firstEnter = 1;
        return self::STRUCT;
    }

    private function newID()
    {

    }

    private function findInRedis()
    {
        return $app['predis']->hgetall( $this->pk );
    }


    private function findInPG()
    {
        return \R::find('users');
    }

}
