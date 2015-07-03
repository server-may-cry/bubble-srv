<?php

$app->get('/loaderio-7e7a5ffb524985036a1106da0bcc9c5e/', function($request, $response) {
    return $response
        //->withStatus(200)

        ->getBody()
        ->rewind()
        ->write('loaderio-7e7a5ffb524985036a1106da0bcc9c5e')
    ;
});
