<?php

namespace Routes;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class StaticFiles {
    public static function action(Application $app, Request $request, $any) {
        $source = fopen(CDN_ROOT.$any, 'r');
        if (!file_exists(dirname(ROOT.'web/bubble/'.$any))) {
            mkdir(dirname(ROOT.'web/bubble/'.$any), 0777, true);
        }
        $dest = fopen(ROOT.'web/bubble/'.$any, 'w');
        stream_copy_to_stream($source, $dest);
        return new RedirectResponse($request->getRequestUri(), 307);
    }

    public static function clear(Application $app) {
        $dir = ROOT.'web/bubble';
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file === '.gitkeep') {
                continue;
            }
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        return $app->json('CLEARED');
    }
}
