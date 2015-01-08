<?php

$host = getenv('BULLET_HOSTNAME');
if($host === false)
	$host = 'http://b.bl';

$tests = array_diff(scandir(__DIR__ . '/test'), array('.','..'));

foreach ($tests as $test) {
	if ( is_file(__DIR__ . '/test/' . $test) ) {
		require __DIR__ . '/test/' . $test;
	}
}