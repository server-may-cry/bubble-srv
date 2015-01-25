<?php

require_once(SRC_ROOT . 'MyTemplate.php');

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

    // R::freeze( ['book','page','book_page'] ); // true
} else {
    /*$log = R::dispense('dumper');
    $log->dateTime = time();
    $log->requestData = str_replace("\n", ' _N_N_ ', var_export($request, true));
    //var_dump($log->requestData);
    $log->format = $request->format();
    $log->raw = $request->raw();
    $log->serialize = serialize($request);
    R::store($log);*/
}
// Throw Exceptions for everything so we can see the errors
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");
// Start user session
//session_start();
// Shortcut to access $app instance anywhere
function app() {
    global $app;
    return $app;
}
// Display exceptions with error and 500 status
$app->on('Exception', function(\Bullet\Request $request, \Bullet\Response $response, \Exception $e) use($app) {
    //if($request->format() === 'json') {
        $data = array(
            'error' => str_replace('Exception', '', get_class($e)),
            'message' => $e->getMessage()
        );
        // Debugging info for development ENV
        if(BULLET_ENV !== 'production') {
            $data['file'] = $e->getFile();
            $data['line'] = $e->getLine();
            $data['trace'] = $e->getTrace();
        }
        $response->content($data);
    //} else {
    //    $response->content($app->template('errors/exception', array('e' => $e))->content());
    //}
    //if(BULLET_ENV === 'production' or true) {
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
    //}
});

// Custom 404 Error Page
$app->on(404, function(\Bullet\Request $request, \Bullet\Response $response) use($app) {
    //$response->content($app->template('errors/404')->content());
    $log = R::dispense('404log');
	$log->request = $request->url();
	$log->dateTime = time();
    $log->raw = $request->raw();
	R::store($log);
	echo json_encode('Not Found');
	die();
	$response->content($app->template('errors/404')->content());
});