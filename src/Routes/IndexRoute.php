<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class IndexRoute
{
    public static function get(Application $app) 
    {
        return $app->json(['foo'=>'bar']);
    }

    public static function post(Application $app, Request $request) 
    {
        $all = requestData($request);
        return $app->json($all);
    }

    public static function favicon(Application $app) 
    {
        return $app->json(null);
    }

    public static function debug(Application $app) 
    {
        $memory = memory_get_peak_usage(true);
        phpinfo();
        die();

        return $app->json(
            [
            'cur_memory' => $memory,
            ]
        );
    }

    public static function test_exception() 
    {
        throw new \InvalidArgumentException('test excemption msg');
    }

    public static function loader(Application $app) 
    {
        return new Response('loaderio-42a36845d21f907d9077524bb26f9a9d');
    }
}
