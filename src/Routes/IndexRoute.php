<?php

namespace Routes;

use Silex\Application;

class IndexRoute {
    public static function get(Application $app) {
        return $app->json(['foo'=>'bar']);
    }

    public static function post(Application $app) {
        return $app->json($request->request->all());
    }

    public static function debug(Application $app) {
        $maxMemory = $app['predis']->get('debug:maxmemory');
        return $app->json([
            'max_memory' => $maxMemory,
        ]);
    }
}
