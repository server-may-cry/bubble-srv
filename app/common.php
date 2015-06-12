<?php
date_default_timezone_set('UTC');
error_reporting(-1); // Display ALL errors
ini_set('display_errors', '1');

// Throw Exceptions for everything so we can see the errors
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");
// Display exceptions with error and 500 status
$app->error(function(\Exception $e) use($app) {
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

	$log = R::dispense('errorlog');
	$log->class = get_class($e);
	$log->message = $e->getMessage();
	$log->file = $e->getFile();
	$log->line = $e->getLine();
	$log->dateTime = time();
	R::store($log);

    render( $data );
});

// Custom 404 Error Page
$app->notFound(function() use($app) {
    $log = R::dispense('404log');
	$log->request = $app->request->getResourceUri();
	$log->dateTime = time();
    $log->raw = json_encode( request() );
	R::store($log);
    render( 'Not Found' . $app->request->getResourceUri() );
});
