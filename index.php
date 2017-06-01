<?php

require_once "vendor/autoload.php";

$fat = \Base::instance();

$fat->config('config.ini');
$fat->config('../fatfree.my.cnf');

$fat->set('system.git', exec('git rev-parse --short HEAD'));

$fat->route('GET /commons', 'route\commons->index');
$fat->route('POST|GET /commons/users', 'route\commons->users_category');
$fat->route('GET /commons/users/@cat', 'route\commons->users_category');
$fat->route('GET /commons/users/@cat/@user', 'route\commons->users_category_detail');
$fat->route('GET /stats', 'route\stats->index');
$fat->route('POST /stats/process', 'route\stats->process');

\helper\formaters::registry();

$fat->run();
