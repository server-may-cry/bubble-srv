<?php

namespace config;

abstract class UserParams
{
    const DEFAULT_REMAINING_TRIES = 5;
    const DEFAULT_CREDITS = 1000;
    const DEFAULT_CREDITS_OK = 3000;
    const INTERVAL_TRIES_RESTORATION = 1800; // 30 minutes
    const FRIENDS_BONUS_CREDITS_MULTIPLIER = 40;
}
