<?php

require_once "vendor/autoload.php";

$fat = \Base::instance();

$fat->config('config.ini');
$fat->config('../fatfree.my.cnf');

$fat->set('system.git', exec('git rev-parse --short HEAD'));

$fat->route('GET /commons', 'route\commons->index');
$fat->route('POST /commons/category', 'route\commons->category_redirect');
$fat->route('GET /commons/category/@category', 'route\commons->category_search');
$fat->route('GET /commons/category/@category/@user', 'route\commons->category_user_search');

$fat->route('GET /stats', 'route\stats->index');
$fat->route('POST /stats/process', 'route\stats->process');

\helper\formaters::registry();

$fat->run();
