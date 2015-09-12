<?php
define('ROOT', dirname(__DIR__) . '/');
define('ROUTE_ROOT', ROOT . 'routes/');

function findUser($uid) {
    
}

// Throw Exceptions for everything so we can see the errors
set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});
