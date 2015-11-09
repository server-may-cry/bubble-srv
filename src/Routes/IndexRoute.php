<?php

namespace Routes;

use Silex\Application;

class IndexRoute {
    public static function get(Application $app) {
        return $app->json(['foo'=>'bar']);
    }
}
