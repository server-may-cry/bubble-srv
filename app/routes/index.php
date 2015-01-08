<?php

$app->path(array('/','index'), function($request) use ($app) {
	// key templateFile not use
	// view bad to use
	// vars not very bad to use
    return ['foo'=>'bar']; // auto json_encode
});