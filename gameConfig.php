<?php

abstract class UserParams {
    public static $defaultUserRemainingTries = 5;
    public static $defaultUserCredits = 700;
    public static $intervalBonusCreditsReceiveTime = 43200;
    public static $bonusCreditsReceive = 1000;
    public static $intervalFriendsBonusCreditsReceiveTime = 86400;
    public static $userFriendsBonusCreditsMultiplier = 30;
}

abstract class IslandLevels {
    public static $count1 = 8;
    public static $count2 = 14;
    public static $count3 = 14;
    public static $count4 = 14;
    public static $count5 = 14;
    public static $count6 = 14;
}

abstract class Market {
    private static $functions = [];

    public static function init()
    {
        self::$functions = [
            'set' => function(&$param, $value){$param = $value;},
            'increase' => function(&$param, $value){$param += $value;},
        ];
    }


    private static $bonus04 = [
        'price' => [
            'vk' => 1500,
        ],
        'titile' => [
            'ru' => 'Открыть все острова',
        ],
    ];
    private static $bonus05 = [
        'price' => [
            'vk' => 750,
        ],
        'reward' => [
            'set' => [
                'ignoreSavePointBlock' => 1,
            ]
        ],
        'photo' => 'bonus05',
        'titile' => [
            'ru' => 'Навсегда разблокировать все флаги',
        ],
    ];
    private static $bonus06 = [
        'price' => [
            'vk' => 200,
        ],
        'titile' => [
            'ru' => 'Открыть следующий остров',
        ],
    ];
    private static $infExt00Lvl3 = [
        'price' => [
            'vk' => 5,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra00' => 1,
            ],
        ],
        'photo' => 'infExt00Lvl3',
        'titile' => [
            'ru' => 'Радужный шар',
        ],
    ];
    private static $infExt01Lvl2 = [
        'price' => [
            'vk' => 10,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra01' => 1,
            ],
        ],
        'photo' => 'infExt01Lvl2',
        'titile' => [
            'ru' => 'Шар-бомба',
        ],
    ];
    private static $infExt02Lvl1 = [
        'price' => [
            'vk' => 20,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra02' => 1,
            ],
        ],
        'photo' => 'infExt02Lvl1',
        'titile' => [
            'ru' => 'Огненный шар',
        ],
    ];
    private static $infExt03Lvl2 = [
        'price' => [
            'vk' => 15,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra03' => 1,
            ],
        ],
        'photo' => 'infExt03Lvl2',
        'titile' => [
            'ru' => 'Горизонтальную молния',
        ],
    ];
    private static $infExt04Lvl1 = [
        'price' => [
            'vk' => 40,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra04' => 1,
            ],
        ],
        'photo' => 'infExt04Lvl1',
        'titile' => [
            'ru' => 'Супер бомба',
        ],
    ];
    private static $infExt05Lvl2 = [
        'price' => [
            'vk' => 25,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra05' => 1,
            ],
        ],
        'photo' => 'infExt05Lvl2',
        'titile' => [
            'ru' => 'Вертикальную молния',
        ],
    ];
    private static $infExt06Lvl1 = [
        'price' => [
            'vk' => 60,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra06' => 1,
            ],
        ],
        'photo' => 'infExt06Lvl1',
        'titile' => [
            'ru' => 'Шар-черная дыра',
        ],
    ];
    private static $infExt07Lvl1 = [
        'price' => [
            'vk' => 70,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra07' => 1,
            ],
        ],
        'photo' => 'infExt07Lvl1',
        'titile' => [
            'ru' => 'Дополнитальные 5 выстрелов',
        ],
    ];
    private static $infExt08Lvl1 = [
        'price' => [
            'vk' => 70,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra08' => 1,
            ],
        ],
        'photo' => 'infExt08Lvl1',
        'titile' => [
            'ru' => 'Дополнительные 30 секунд',
        ],
    ];
    private static $infExt09Lvl1 = [
        'price' => [
            'vk' => 70,
        ],
        'reward' => [
            'set' => [
                'inifinityExtra09' => 1,
            ],
        ],
        'photo' => 'infExt09Lvl1',
        'titile' => [
            'ru' => 'Иммунитет к ядовитым шарам',
        ],
    ];
    private static $helpPack01 = [
        'price' => [
            'vk' => 15,
        ],
        'reward' => [
            'increase' => [
                'remainingTries' => 10,
                'credits' => 420,
            ],
        ],
        'photo' => 'helpPack01',
        'titile' => [
            'ru' => 'Экстра помощь',
        ],
    ];
    private static $additionalShots = [
        'price' => [
            'vk' => 1,
        ],
        'reward' => [],
        'photo' => 'additionalShots',
        'titile' => [
            'ru' => 'Дополнительные выстрелы',
        ],
    ];
    private static $additionalTime = [
        'price' => [
            'vk' => 1,
        ],
        'reward' => [],
        'photo' => 'additionalTime',
        'titile' => [
            'ru' => 'Дополнительное время',
        ],
    ];
    private static $creditsPack01 = [
        'price' => [
            'vk' => 2,
        ],
        'reward' => [
            'increase' => [
                'credits' => 30,
            ],
        ],
        'photo' => 'creditsPack01',
        'titile' => [
            'ru' => '30 золотых монет',
        ],
    ];
    private static $creditsPack02 = [
        'price' => [
            'vk' => 4,
        ],
        'reward' => [
            'increase' => [
                'credits' => 70,
            ],
        ],
        'photo' => 'creditsPack02',
        'titile' => [
            'ru' => '70 золотых монет',
        ],
    ];
    private static $creditsPack03 = [
        'price' => [
            'vk' => 6,
        ],
        'reward' => [
            'increase' => [
                'credits' => 140,
            ],
        ],
        'photo' => 'creditsPack03',
        'titile' => [
            'ru' => '140 золотых монет',
        ],
    ];
    private static $creditsPack04 = [
        'price' => [
            'vk' => 8,
        ],
        'reward' => [
            'increase' => [
                'credits' => 280,
            ],
        ],
        'photo' => 'creditsPack04',
        'titile' => [
            'ru' => '280 золотых монет',
        ],
    ];
    private static $creditsPack05 = [
        'price' => [
            'vk' => 10,
        ],
        'reward' => [
            'increase' => [
                'credits' => 420,
            ],
        ],
        'photo' => 'creditsPack05',
        'titile' => [
            'ru' => '420 золотых монет',
        ],
    ];

    public static function buy($user, $itemName, $platform, $paid = null)
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
            R::fancyDebug(true);
            ob_start();
            error_log('before '.var_export($user->credits, true));
            R::store($user);
            error_log('after '.var_export($user->credits, true));
            $c = ob_get_clean();
            error_log($c);
            $user->credits += 1;
            $user->credits -= 1;
            R::store($user);
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

        if(isset($item['titile'][$lang])) {
            $item['titile'] = $item['titile'][$lang];
        } else {
            //enable on production $item['titile'] = $item['titile']['en'];
        }

        if(strlen($item['photo'])) {
            $item['photo'] = 'http://119226.selcdn.com/bubble/productIcons/' . $item['photo'] . '.png';
        }
        return $item;
    }
}
Market::init();
