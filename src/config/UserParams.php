<?php

namespace config;

abstract class UserParams {
    public static $defaultUserRemainingTries = 5;
    public static $defaultUserCredits = 1000;
    public static $intervalFriendsBonusCreditsReceiveTime = 84600; // 23,5 hours
    public static $userFriendsBonusCreditsMultiplier = 40;
}
