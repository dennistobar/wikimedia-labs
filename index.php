<?php

require_once "vendor/autoload.php";

$f3 = \Base::instance();

$f3->config('config.ini');
$f3->config('../fatfree.my.cnf');

$f3->route('GET /commons', 'route\commons->index');
$f3->route('POST|GET /commons/users', 'route\commons->users_category');
$f3->route('GET /commons/users/@cat', 'route\commons->users_category');
$f3->route('GET /commons/users/@cat/@user', 'route\commons->users_category_detail');
$f3->route('GET /stats', 'route\stats->index');
$f3->route('POST /stats/process', 'route\stats->process');

\helper\formaters::registry();

$f3->run();
