<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class IndexRoute {
    public static function get(Application $app) {
        return $app->json(['foo'=>'bar']);
    }

    public static function post(Application $app, Request $request) {
        return $app->json($request->request->all());
    }

    public static function debug(Application $app) {
        $memory = memory_get_peak_usage(true);

        return $app->json([
            'cur_memory' => $memory,
        ]);
    }

    public static function loader(Application $app) {
        return new Response('loaderio-b1605c8654686a992bd3968349d85b8e');
        // loaderio-a1605b7f59f37748149caae19249ff85
    }
}
