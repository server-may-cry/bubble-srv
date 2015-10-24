<?php

namespace config;

abstract class UserParams {
    const DEFAULT_REMAINING_TRIES = 5;
    const DEFAULT_CREDITS = 1000;
    const INTERVAL_TRIES_RESTORATION = 3600; // 1 hour
    const FRIENDS_BONUS_CREDITS_MULTIPLIER = 40;
}
