<?php

use Symfony\Component\HttpFoundation\Request;
use Alchemy\Zippy\Zippy;

$start = microtime(true);
$src = fopen(CDN_ROOT.'bubble.zip?'.time(), 'r');
$trg = fopen(ROOT.'bubble.zip', 'w');
stream_copy_to_stream($src, $trg);
fclose($src);
fclose($trg);
$time = microtime(true) - $start;
$contains[] = sprintf('Загрузка %.2F сек.', $time);

$start = microtime(true);
$zippy = Zippy::load();
$archive = $zippy->open(ROOT.'bubble.zip');
$archive->extract(ROOT.'web/bubble');
$contains = [];
unlink(ROOT.'bubble.zip');
$time = microtime(true) - $start;
$contains[] = sprintf('Распаковка %.2F сек.', $time);

print_r($contains);
