<?php

namespace config;

use Silex\Application;
use config\UserParams;

abstract class Market
{

    private static $functions = [];

    public static function init()
    {
        self::$functions = [
            'set' => function (&$param, $value) {
                $param = $value;
            },
            'increase' => function (&$param, $value) {
                $param += $value;
            },
        ];
    }

    public static function buy(Application $app, $user, $itemName, $platform = null, $paid = null)
    {
        if(!isset(self::$items[$itemName])) {
            throw new Exception("Unknown item ".$itemName);
        }
        $item = self::$items[$itemName];
        if($paid and $paid != $item['price'][$platform]) {
            throw new Exception("Incorrect price ".$paid." expect ".$item['price'][$platform]);
        }
        if(isset($item['reward'])) {
            foreach($item['reward'] as $action => $reward) {
                foreach($reward as $name => $value) {
                    call_user_func_array(self::$functions[$action], [&$user->$name, $value]);
                }
            }
            if($user->remainingTries >= UserParams::DEFAULT_REMAINING_TRIES) {
                $user->restoreTriesAt = 0;
            }
            $user->extId = $user->extId; // red bean fix
            $user->credits = max($user->credits, 0);
            \R::store($user);
        } else {
            // HARDCODE
            throw new Exception('This item ('.$itemName.') not configured');
        }
    }

    public static function info($itemName, $platform, $lang)
    {
        if(!isset(self::$items[$itemName])) {
            throw new Exception("Unknown item ".$itemName);
        }
        $item = self::$items[$itemName];
        if(!isset($item['price'][$platform])) {
            throw new Exception("Unknown platform ".$platform." on item ".$itemName);
        }
        $item['price'] = $item['price'][$platform];

        if(isset($item['title'][$lang])) {
            $item['title'] = $item['title'][$lang];
        } else {
            $item['title'] = $item['title']['en'];
        }

        if(isset($item['photo'])) {
            $item['photo'] = CDN_ROOT.'productIcons/' . $item['photo'] . '.png';
        }
        return $item;
    }

    private static $items = array (
      'bonus04_1' => 
      array (
        'price' => 
        array (
          'vk' => 100,
          'ok' => 769,
        ),
        'title' => 
        array (
          'ru' => 'Открыть все острова',
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'reachedStage01' => 7,
            'reachedSubStage01' => 14,
          ),
        ),
      ),
      'bonus04_2' => 
      array (
        'price' => 
        array (
          'vk' => 100,
          'ok' => 769,
        ),
        'title' => 
        array (
          'ru' => 'Открыть все острова',
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'reachedStage02' => 7,
            'reachedSubStage02' => 14,
          ),
        ),
      ),
      'bonus05' => 
      array (
        'price' => 
        array (
          'vk' => 20,
          'ok' => 449,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'ignoreSavePointBlock' => 1,
          ),
        ),
        'photo' => 'bonus05',
        'title' => 
        array (
          'ru' => 'Навсегда разблокировать все флаги',
        ),
      ),
      'bonus06_1' => 
      array (
        'price' => 
        array (
          'vk' => 10,
          'ok' => 299,
        ),
        'title' => 
        array (
          'ru' => 'Открыть следующий остров',
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'reachedSubStage01' => 0,
          ),
          'increase' => 
          array (
            'reachedStage01' => 1,
          ),
        ),
      ),
      'bonus06_2' => 
      array (
        'price' => 
        array (
          'vk' => 10,
          'ok' => 299,
        ),
        'title' => 
        array (
          'ru' => 'Открыть следующий остров',
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'reachedSubStage02' => 0,
          ),
          'increase' => 
          array (
            'reachedStage02' => 1,
          ),
        ),
      ),
      'infExt00Lvl3' => 
      array (
        'price' => 
        array (
          'vk' => 1,
          'ok' => 29,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra00' => 1,
          ),
        ),
        'photo' => 'infExt00Lvl3',
        'title' => 
        array (
          'ru' => 'Радужный шар',
        ),
      ),
      'infExt01Lvl2' => 
      array (
        'price' => 
        array (
          'vk' => 1,
          'ok' => 29,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra01' => 1,
          ),
        ),
        'photo' => 'infExt01Lvl2',
        'title' => 
        array (
          'ru' => 'Шар-бомба',
        ),
      ),
      'infExt02Lvl1' => 
      array (
        'price' => 
        array (
          'vk' => 3,
          'ok' => 89,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra02' => 1,
          ),
        ),
        'photo' => 'infExt02Lvl1',
        'title' => 
        array (
          'ru' => 'Огненный шар',
        ),
      ),
      'infExt03Lvl2' => 
      array (
        'price' => 
        array (
          'vk' => 2,
          'ok' => 58,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra03' => 1,
          ),
        ),
        'photo' => 'infExt03Lvl2',
        'title' => 
        array (
          'ru' => 'Горизонтальную молния',
        ),
      ),
      'infExt04Lvl1' => 
      array (
        'price' => 
        array (
          'vk' => 3,
          'ok' => 89,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra04' => 1,
          ),
        ),
        'photo' => 'infExt04Lvl1',
        'title' => 
        array (
          'ru' => 'Супер бомба',
        ),
      ),
      'infExt05Lvl2' => 
      array (
        'price' => 
        array (
          'vk' => 2,
          'ok' => 58,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra05' => 1,
          ),
        ),
        'photo' => 'infExt05Lvl2',
        'title' => 
        array (
          'ru' => 'Вертикальную молния',
        ),
      ),
      'infExt06Lvl1' => 
      array (
        'price' => 
        array (
          'vk' => 3,
          'ok' => 89,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra06' => 1,
          ),
        ),
        'photo' => 'infExt06Lvl1',
        'title' => 
        array (
          'ru' => 'Шар-черная дыра',
        ),
      ),
      'infExt07Lvl1' => 
      array (
        'price' => 
        array (
          'vk' => 4,
          'ok' => 116,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra07' => 1,
          ),
        ),
        'photo' => 'infExt07Lvl1',
        'title' => 
        array (
          'ru' => 'Дополнитальные 5 выстрелов',
        ),
      ),
      'infExt08Lvl1' => 
      array (
        'price' => 
        array (
          'vk' => 4,
          'ok' => 116,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra08' => 1,
          ),
        ),
        'photo' => 'infExt08Lvl1',
        'title' => 
        array (
          'ru' => 'Дополнительные 30 секунд',
        ),
      ),
      'infExt09Lvl1' => 
      array (
        'price' => 
        array (
          'vk' => 3,
          'ok' => 89,
        ),
        'reward' => 
        array (
          'set' => 
          array (
            'inifinityExtra09' => 1,
          ),
        ),
        'photo' => 'infExt09Lvl1',
        'title' => 
        array (
          'ru' => 'Иммунитет к ядовитым шарам',
        ),
      ),
      'helpPack01' => 
      array (
        'price' => 
        array (
          'vk' => 1,
          'ok' => 29,
        ),
        'reward' => 
        array (
          'increase' => 
          array (
            'remainingTries' => 5,
            'credits' => 650,
          ),
        ),
        'photo' => 'helpPack01',
        'title' => 
        array (
          'ru' => 'Экстра помощь',
        ),
      ),
      'additionalShots' => 
      array (
        'price' => 
        array (
          'vk' => 1,
          'ok' => 89,
        ),
        'reward' => 
        array (
        ),
        'photo' => 'additionalShots',
        'title' => 
        array (
          'ru' => 'Дополнительные выстрелы',
        ),
      ),
      'additionalTime' => 
      array (
        'price' => 
        array (
          'vk' => 1,
          'ok' => 89,
        ),
        'reward' => 
        array (
        ),
        'photo' => 'additionalTime',
        'title' => 
        array (
          'ru' => 'Дополнительное время',
        ),
      ),
      'creditsPack01' => 
      array (
        'price' => 
        array (
          'vk' => 1,
          'ok' => 29,
        ),
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => 140,
          ),
        ),
        'photo' => 'creditsPack01',
        'title' => 
        array (
          'ru' => '140 золотых монет',
        ),
      ),
      'creditsPack01_test' => 
      array (
        'price' => 
        array (
          'vk' => 1,
          'ok' => 1,
        ),
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => 1,
          ),
        ),
        'photo' => 'creditsPack01',
        'title' => 
        array (
          'ru' => 'Тестовая покупка одной монеты',
        ),
      ),
      'creditsPack02' => 
      array (
        'price' => 
        array (
          'vk' => 2,
          'ok' => 89,
        ),
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => 650,
          ),
        ),
        'photo' => 'creditsPack02',
        'title' => 
        array (
          'ru' => '650 золотых монет',
        ),
      ),
      'creditsPack03' => 
      array (
        'price' => 
        array (
          'vk' => 9,
          'ok' => 299,
        ),
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => 3000,
          ),
        ),
        'photo' => 'creditsPack03',
        'title' => 
        array (
          'ru' => '3000 золотых монет',
        ),
      ),
      'creditsPack04' => 
      array (
        'price' => 
        array (
          'vk' => 16,
          'ok' => 449,
        ),
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => 6000,
          ),
        ),
        'photo' => 'creditsPack04',
        'title' => 
        array (
          'ru' => '6000 золотых монет',
        ),
      ),
      'creditsPack05' => 
      array (
        'price' => 
        array (
          'vk' => 34,
          'ok' => 769,
        ),
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => 16000,
          ),
        ),
        'photo' => 'creditsPack05',
        'title' => 
        array (
          'ru' => '16000 золотых монет',
        ),
      ),
      'bonus01' => 
      array (
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => -75,
            'remainingTries' => 1,
          ),
        ),
      ),
      'bonus02' => 
      array (
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => -300,
            'remainingTries' => 5,
          ),
        ),
      ),
      'bonus03' => 
      array (
        'reward' => 
        array (
          'increase' => 
          array (
            'credits' => -500,
            'remainingTries' => 10,
          ),
        ),
      ),
    );
}
Market::init();
