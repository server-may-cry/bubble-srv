<?php
define('ROOT', dirname(__DIR__).'/');
define('ROUTE_ROOT', ROOT.'routes/');
define('CDN_ROOT', 'http://119226.selcdn.com/bubble/');
define('REDIS_CACHE_TIME_ISLANDS', 3600); // 1 hour

function findUser($uid) {
    $user = R::findOne('users', 'id = ?', [(int)$uid]);

    if($user === NULL)
        throw new Exception("UserID: ".$uid.' not found');
    return $user;
}

function requestData(Symfony\Component\HttpFoundation\Request $request) {
	return $request->request->all();
}

// Throw Exceptions for everything so we can see the errors
set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});
