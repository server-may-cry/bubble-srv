<?php

namespace config;

use Silex\Application;

abstract class Market
{

    private static $functions = [];

    public static function init()
    {
        self::$functions = [
            'set' => function(&$param, $value){$param = $value;},
            'increase' => function(&$param, $value){$param += $value;},
        ];
    }

    private static $bonus04_1 = [
        'price' => [
            'vk' => 100,
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
    ];
    private static $bonus04_2 = [
        'price' => [
            'vk' => 100,
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
    ];
    private static $bonus05 = [
        'price' => [
            'vk' => 20,
        ],
        'reward' => [
            'set' => [
                'ignoreSavePointBlock' => 1,
            ]
        ],
        'photo' => 'bonus05',
        'title' => [
            'ru' => 'Навсегда разблокировать все флаги',
        ],
    ];
    private static $bonus06_1 = [
        'price' => [
            'vk' => 10,
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
            ]
        ],
    ];
    private static $bonus06_2 = [
        'price' => [
            'vk' => 10,
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
            ]
        ],
    ];
    private static $infExt00Lvl3 = [
        'price' => [
            'vk' => 1,
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
    ];
    private static $infExt01Lvl2 = [
        'price' => [
            'vk' => 1,
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
    ];
    private static $infExt02Lvl1 = [
        'price' => [
            'vk' => 3,
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
    ];
    private static $infExt03Lvl2 = [
        'price' => [
            'vk' => 2,
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
    ];
    private static $infExt04Lvl1 = [
        'price' => [
            'vk' => 3,
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
    ];
    private static $infExt05Lvl2 = [
        'price' => [
            'vk' => 2,
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
    ];
    private static $infExt06Lvl1 = [
        'price' => [
            'vk' => 3,
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
    ];
    private static $infExt07Lvl1 = [
        'price' => [
            'vk' => 4,
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
    ];
    private static $infExt08Lvl1 = [
        'price' => [
            'vk' => 4,
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
    ];
    private static $infExt09Lvl1 = [
        'price' => [
            'vk' => 3,
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
    ];
    private static $helpPack01 = [
        'price' => [
            'vk' => 1,
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
    ];
    private static $additionalShots = [
        'price' => [
            'vk' => 1,
        ],
        'reward' => [],
        'photo' => 'additionalShots',
        'title' => [
            'ru' => 'Дополнительные выстрелы',
        ],
    ];
    private static $additionalTime = [
        'price' => [
            'vk' => 1,
        ],
        'reward' => [],
        'photo' => 'additionalTime',
        'title' => [
            'ru' => 'Дополнительное время',
        ],
    ];
    private static $creditsPack01 = [
        'price' => [
            'vk' => 1,
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
    ];
    private static $creditsPack02 = [
        'price' => [
            'vk' => 2,
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
    ];
    private static $creditsPack03 = [
        'price' => [
            'vk' => 9,
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
    ];
    private static $creditsPack04 = [
        'price' => [
            'vk' => 16,
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
    ];
    private static $creditsPack05 = [
        'price' => [
            'vk' => 34,
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
    ];

    // in game
    public static $bonus01 = [
        'reward' => [
            'increase' => [
                'credits' => -75,
                'remainingTries' => 1,
            ],
        ]
    ];
    public static $bonus02 = [
        'reward' => [
            'increase' => [
                'credits' => -300,
                'remainingTries' => 5,
            ],
        ]
    ];
    public static $bonus03 = [
        'reward' => [
            'increase' => [
                'credits' => -500,
                'remainingTries' => 10,
            ],
        ]
    ];

    public static function buy(Application $app, $user, $itemName, $platform = null, $paid = null)
    {
        if(!isset(self::$$itemName)) {
            throw new Exception("Unknown item ".$itemName);
        }
        $item = self::$$itemName;
        if($paid and $paid != $item['price'][$platform]) {
            throw new Exception("Incorrect price ".$paid." expect ".$item['price'][$platform]);
        }
        if(isset($item['reward'])) {
            foreach($item['reward'] as $action => $reward) {
                foreach($reward as $name => $value) {
                    call_user_func_array( self::$functions[$action], [&$user->$name, $value] );
                }
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
        if(!isset(self::$$itemName)) {
            throw new Exception("Unknown item ".$itemName);
        }
        $item = self::$$itemName;
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

}
Market::init();
