<?php

// Setup defaults...
date_default_timezone_set('UTC');
error_reporting(-1); // Display ALL errors
ini_set('display_errors', '1');
ini_set("session.cookie_httponly", '1'); // Mitigate XSS javascript cookie attacks for browers that support it
ini_set("session.use_only_cookies", '1'); // Don't allow session_id in URLs
// ENV globals
define('BULLET_ENV', $request->env('BULLET_ENV', 'development'));
// Production setting switch
if(BULLET_ENV == 'production') {
    // Hide errors in production
    error_reporting(0);
    ini_set('display_errors', '0');
}
// Throw Exceptions for everything so we can see the errors
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");
// Shortcut to access $app instance anywhere
function app() {
    global $app;
    return $app;
}
// Display exceptions with error and 500 status
$app->on('Exception', function(\Bullet\Request $request, \Bullet\Response $response, \Exception $e) use($app) {
    $data = array(
        'error' => get_class($e),
        'message' => $e->getMessage()
    );
    // Debugging info for development ENV
    if(BULLET_ENV !== 'production') {
        $data['file'] = $e->getFile();
        $data['line'] = $e->getLine();
        //$data['trace'] = $e->getTrace();
    }
    $response->content($data);
    if(BULLET_ENV === 'production') {
        // make error store to sentry $e $request->uri() $request->raw()
    } else {
    	$log = R::dispense('errorlog');
    	$log->class = str_replace('Exception', 'E', get_class($e));
    	$log->message = $e->getMessage();
    	$log->file = $e->getFile();
    	$log->line = $e->getLine();
    	$log->trace = json_encode($e->getTrace());
		$log->dateTime = time();
		R::store($log);
        // An error happened in production. You should really let yourself know about it.
        // TODO: Email, log to file, or send to error-logging service like Sentry, Airbrake, etc.
    }
});

// Custom 404 Error Page
$app->on(404, function(\Bullet\Request $request, \Bullet\Response $response) use($app) {
    $log = R::dispense('404log');
	$log->request = $request->url();
	$log->dateTime = time();
    $log->raw = $request->raw();
	R::store($log);
	echo json_encode('Not Found');
	die();
});
