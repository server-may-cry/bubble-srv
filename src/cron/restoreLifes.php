<?php

require dirname(__DIR__).'/global.php';
require ROOT.'src/db.php';
require_once ROOT.'vendor/autoload.php'; // for predis client

restoreLifes($redis_exist);
