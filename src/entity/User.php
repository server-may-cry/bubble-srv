<?php

namespace entity;

use Silex\Application;
use config\UserParams;

final class User
{

    const STRUCT = [
        'id' => '0',
        'tries' => (string) UserParams::$defaultUserRemainingTries,
        'credits' => (string) UserParams::$defaultUserCredits,
        'friendsBonusCreditsTime' => (string) time(),
        'flags' => '0',
    ];
    private $pk = ''; // save string, user primary key
    private $data = [];

    public function __construct(Application $app, $platform, $id)
    {
        $platform = 'VK';
        $id = (int)$id;
        $this->pk = "{$platform}:{$id}";
        $this->data = $this->find();
    }

    public function addLifes()
    {

    }

    public function addCoins()
    {

    }

    public function buyGood()
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

        // mew user
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

    }

}
