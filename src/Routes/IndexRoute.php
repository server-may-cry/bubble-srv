<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

abstract class IndexRoute {
    public static function get(Application $app) {
        return $app->json(['foo'=>'bar']);
    }

    public static function post(Application $app, Request $request) {
        return $app->json($request->request->all());
    }

    public static function debug(Application $app) {
        $maxMemory = $app['predis']->get('debug:maxmemory');
        $memory = memory_get_peak_usage(true);

        return $app->json([
            'max_memory' => $maxMemory,
            'cur_memory' => $memory,
        ]);
    }
}
