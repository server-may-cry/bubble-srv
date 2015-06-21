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
    $data = [
        'error' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ];
    if(ENV_NAME === 'production') {
        $client = new \Raygun4php\RaygunClient(getenv('RAYGUN_APIKEY'));
        $client->SendException($e);

        Rollbar::init(array('access_token' => getenv('ROLLBAR_ACCESS_TOKEN')));
        Rollbar::report_exception($e);

        $client = new Raven_Client(getenv('SENTRY_RAVEN_URL'), [
            // pass along the version of your application
            // 'release' => '1.0.0',
        ]);
        $client->captureException($e);
        
    }

    render( $data, 400 );
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
