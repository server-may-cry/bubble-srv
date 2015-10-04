<?php

use Symfony\Component\HttpFoundation\Request;
use Alchemy\Zippy\Zippy;

$app->post('/upload', function() use ($app) {
    $start = microtime(true);
    $src = fopen(CDN_ROOT.'bubble.zip', 'r');
    $trg = fopen(ROOT.'bubble.zip', 'w');
    while($content = fread($src, 10240)) {
        fwrite($trg, $content);
    }
    $zippy = Zippy::load();
    $archive = $zippy->open('bubble.zip');
    $archive->extract(ROOT.'web/bubble');
    $contains = [];
    foreach ($archive as $member) {
        $contains[] = (string) $member;
    }
    unlink(ROOT.'bubble.zip');
    $time = microtime(true) - $start;
    $contains[] = sprintf('Скрипт выполнялся %.4F сек.', $time);
    return $app->json($contains);
});
