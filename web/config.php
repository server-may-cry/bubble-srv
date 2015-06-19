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

abstract class MarketParams {
    public static $bonus04 = [
        'price' => [
            'vk' => 1500,
        ],
    ];
    public static $bonus05 = [
        'price' => [
            'vk' => 750,
        ],
    ];
    public static $bonus06 = [
        'price' => [
            'vk' => 200,
        ],
    ];
    public static $infExt00Lvl3 = [
        'price' => [
            'vk' => 5,
        ],
    ];
    public static $infExt01Lvl2 = [
        'price' => [
            'vk' => 10,
        ],
    ];
    public static $infExt02Lvl1 = [
        'price' => [
            'vk' => 20,
        ],
    ];
    public static $infExt03Lvl2 = [
        'price' => [
            'vk' => 15,
        ],
    ];
    public static $infExt04Lvl1 = [
        'price' => [
            'vk' => 40,
        ],
    ];
    public static $infExt05Lvl2 = [
        'price' => [
            'vk' => 25,
        ],
    ];
    public static $infExt06Lvl1 = [
        'price' => [
            'vk' => 60,
        ],
    ];
    public static $infExt07Lvl1 = [
        'price' => [
            'vk' => 70,
        ],
    ];
    public static $infExt08Lvl1 = [
        'price' => [
            'vk' => 70,
        ],
    ];
    public static $infExt09Lvl1 = [
        'price' => [
            'vk' => 70,
        ],
    ];
    public static $helpPack01 = [
        'price' => [
            'vk' => 15,
        ],
    ];
    public static $additionalShots = [
        'price' => [
            'vk' => 1,
        ],
    ];
    public static $additionalTime = [
        'price' => [
            'vk' => 1,
        ],
    ];
    public static $creditsPack01 = [
        'price' => [
            'vk' => 2,
        ],
    ];
    public static $creditsPack02 = [
        'price' => [
            'vk' => 4,
        ],
    ];
    public static $creditsPack03 = [
        'price' => [
            'vk' => 6,
        ],
    ];
    public static $creditsPack04 = [
        'price' => [
            'vk' => 8,
        ],
    ];
    public static $creditsPack05 = [
        'price' => [
            'vk' => 10,
        ],
    ];
}
