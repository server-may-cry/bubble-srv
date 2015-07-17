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
    ];
    private static $bonus05 = [
        'price' => [
            'vk' => 750,
        ],
        'reward' => [
            'set' => [
                'ignoreSavePointBlock' => 1,
            ]
        ]
    ];
    private static $bonus06 = [
        'price' => [
            'vk' => 200,
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
    ];
    private static $helpPack01 = [
        'title' => [
            'en' => 'Extra help pack',
        ],
        'price' => [
            'vk' => 15,
        ],
        'reward' => [
            'increase' => [
                'remainingTries' => 10,
                'credits' => 420,
            ],
        ],
    ];
    private static $additionalShots = [
        'price' => [
            'vk' => 1,
        ],
        'reward' => [],
    ];
    private static $additionalTime = [
        'price' => [
            'vk' => 1,
        ],
        'reward' => [],
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
            R::debug( TRUE );
            ob_start();
            error_log('before '.var_export($user->credits, true));
            error_log('before user '.var_export($user, true));
            $rst = R::store($user);
            error_log('after '.var_export($user->credits, true));
            error_log('after user '.var_export($user, true));
            error_log('rst '.var_export($rst, true));
            $c = ob_get_clean();
            error_log($c);
            $user->credits += 1;
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

        if(isset($item['photo_url'][$platform])) {
            $item['photo_url'] = $item['photo_url'][$platform];
        } else {
            //enable on production $item['photo_url'] = $item['photo_url']['vk'];
        }
        return $item;
    }
}
Market::init();
