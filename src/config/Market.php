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

    static $items = [
      'bonus04_1' => [
        'price' => [
          'vk' => 100,
          'ok' => 769,
        ],
        'title' => [
          'ru' => 'Открыть все острова',
        ],
        'reward' => [
          'set' => [
            'reachedStage01' => 7,
            'reachedSubStage01' => 14,
          ],
        ],
      ],
      'bonus04_2' => [
        'price' => [
          'vk' => 100,
          'ok' => 769,
        ],
        'title' => [
          'ru' => 'Открыть все острова',
        ],
        'reward' => [
          'set' => [
            'reachedStage02' => 7,
            'reachedSubStage02' => 14,
          ],
        ],
      ],
      'bonus05' => [
        'price' => [
          'vk' => 20,
          'ok' => 449,
        ],
        'reward' => [
          'set' => [
            'ignoreSavePointBlock' => 1,
          ],
        ],
        'photo' => 'bonus05',
        'title' => [
          'ru' => 'Навсегда разблокировать все флаги',
        ],
      ],
      'bonus06_1' => [
        'price' => [
          'vk' => 10,
          'ok' => 299,
        ],
        'title' => [
          'ru' => 'Открыть следующий остров',
        ],
        'reward' => [
          'set' => [
            'reachedSubStage01' => 0,
          ],
          'increase' => [
            'reachedStage01' => 1,
          ],
        ],
      ],
      'bonus06_2' => [
        'price' => [
          'vk' => 10,
          'ok' => 299,
        ],
        'title' => [
          'ru' => 'Открыть следующий остров',
        ],
        'reward' => [
          'set' => [
            'reachedSubStage02' => 0,
          ],
          'increase' => [
            'reachedStage02' => 1,
          ],
        ],
      ],
      'infExt00Lvl3' => [
        'price' => [
          'vk' => 1,
          'ok' => 29,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra00' => 1,
          ],
        ],
        'photo' => 'infExt00Lvl3',
        'title' => [
          'ru' => 'Радужный шар',
        ],
      ],
      'infExt01Lvl2' => [
        'price' => [
          'vk' => 1,
          'ok' => 29,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra01' => 1,
          ],
        ],
        'photo' => 'infExt01Lvl2',
        'title' => [
          'ru' => 'Шар-бомба',
        ],
      ],
      'infExt02Lvl1' => [
        'price' => [
          'vk' => 3,
          'ok' => 89,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra02' => 1,
          ],
        ],
        'photo' => 'infExt02Lvl1',
        'title' => [
          'ru' => 'Огненный шар',
        ],
      ],
      'infExt03Lvl2' => [
        'price' => [
          'vk' => 2,
          'ok' => 58,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra03' => 1,
          ],
        ],
        'photo' => 'infExt03Lvl2',
        'title' => [
          'ru' => 'Горизонтальную молния',
        ],
      ],
      'infExt04Lvl1' => [
        'price' => [
          'vk' => 3,
          'ok' => 89,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra04' => 1,
          ],
        ],
        'photo' => 'infExt04Lvl1',
        'title' => [
          'ru' => 'Супер бомба',
        ],
      ],
      'infExt05Lvl2' => [
        'price' => [
          'vk' => 2,
          'ok' => 58,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra05' => 1,
          ],
        ],
        'photo' => 'infExt05Lvl2',
        'title' => [
          'ru' => 'Вертикальную молния',
        ],
      ],
      'infExt06Lvl1' => [
        'price' => [
          'vk' => 3,
          'ok' => 89,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra06' => 1,
          ],
        ],
        'photo' => 'infExt06Lvl1',
        'title' => [
          'ru' => 'Шар-черная дыра',
        ],
      ],
      'infExt07Lvl1' => [
        'price' => [
          'vk' => 4,
          'ok' => 116,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra07' => 1,
          ],
        ],
        'photo' => 'infExt07Lvl1',
        'title' => [
          'ru' => 'Дополнитальные 5 выстрелов',
        ],
      ],
      'infExt08Lvl1' => [
        'price' => [
          'vk' => 4,
          'ok' => 116,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra08' => 1,
          ],
        ],
        'photo' => 'infExt08Lvl1',
        'title' => [
          'ru' => 'Дополнительные 30 секунд',
        ],
      ],
      'infExt09Lvl1' => [
        'price' => [
          'vk' => 3,
          'ok' => 89,
        ],
        'reward' => [
          'set' => [
            'inifinityExtra09' => 1,
          ],
        ],
        'photo' => 'infExt09Lvl1',
        'title' => [
          'ru' => 'Иммунитет к ядовитым шарам',
        ],
      ],
      'helpPack01' => [
        'price' => [
          'vk' => 1,
          'ok' => 29,
        ],
        'reward' => [
          'increase' => [
            'remainingTries' => 5,
            'credits' => 650,
          ],
        ],
        'photo' => 'helpPack01',
        'title' => [
          'ru' => 'Экстра помощь',
        ],
      ],
      'additionalShots' => [
        'price' => [
          'vk' => 1,
          'ok' => 89,
        ],
        'reward' => [
        ],
        'photo' => 'additionalShots',
        'title' => [
          'ru' => 'Дополнительные выстрелы',
        ],
      ],
      'additionalTime' => [
        'price' => [
          'vk' => 1,
          'ok' => 89,
        ],
        'reward' => [
        ],
        'photo' => 'additionalTime',
        'title' => [
          'ru' => 'Дополнительное время',
        ],
      ],
      'creditsPack01' => [
        'price' => [
          'vk' => 1,
          'ok' => 29,
        ],
        'reward' => [
          'increase' => [
            'credits' => 140,
          ],
        ],
        'photo' => 'creditsPack01',
        'title' => [
          'ru' => '140 золотых монет',
        ],
      ],
      'creditsPack01_test' => [
        'price' => [
          'vk' => 1,
          'ok' => 1,
        ],
        'reward' => [
          'increase' => [
            'credits' => 1,
          ],
        ],
        'photo' => 'creditsPack01',
        'title' => [
          'ru' => 'Тестовая покупка одной монеты',
        ],
      ],
      'creditsPack02' => [
        'price' => [
          'vk' => 2,
          'ok' => 89,
        ],
        'reward' => [
          'increase' => [
            'credits' => 650,
          ],
        ],
        'photo' => 'creditsPack02',
        'title' => [
          'ru' => '650 золотых монет',
        ],
      ],
      'creditsPack03' => [
        'price' => [
          'vk' => 9,
          'ok' => 299,
        ],
        'reward' => [
          'increase' => [
            'credits' => 3000,
          ],
        ],
        'photo' => 'creditsPack03',
        'title' => [
          'ru' => '3000 золотых монет',
        ],
      ],
      'creditsPack04' => [
        'price' => [
          'vk' => 16,
          'ok' => 449,
        ],
        'reward' => [
          'increase' => [
            'credits' => 6000,
          ],
        ],
        'photo' => 'creditsPack04',
        'title' => [
          'ru' => '6000 золотых монет',
        ],
      ],
      'creditsPack05' => [
        'price' => [
          'vk' => 34,
          'ok' => 769,
        ],
        'reward' => [
          'increase' => [
            'credits' => 16000,
          ],
        ],
        'photo' => 'creditsPack05',
        'title' => [
          'ru' => '16000 золотых монет',
        ],
      ],
      'bonus01' => [
        'reward' => [
          'increase' => [
            'credits' => -75,
            'remainingTries' => 1,
          ],
        ],
      ],
      'bonus02' => [
        'reward' => [
          'increase' => [
            'credits' => -300,
            'remainingTries' => 5,
          ],
        ],
      ],
      'bonus03' => [
        'reward' => [
          'increase' => [
            'credits' => -500,
            'remainingTries' => 10,
          ],
        ],
      ],
    ];
}
Market::init();
