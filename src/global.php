<?php
define('ROOT', dirname(__DIR__).'/');
define('ROUTE_ROOT', ROOT.'routes/');
define('CDN_ROOT', 'http://119226.selcdn.com/bubble/');
define('CACHE_TIME_ISLANDS', 3600); // 1 hour

function findUser($uid) {
    $user = \R::findOne('users', 'id = ?', [(int)$uid]);

    if($user === NULL)
        throw new Exception("UserID: ".$uid.' not found');
    return $user;
}

function requestData(Symfony\Component\HttpFoundation\Request $request) {
	return json_decode($request->getContent(), true);
}

Symfony\Component\Debug\ErrorHandler::register();
