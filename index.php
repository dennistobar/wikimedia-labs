<?php

require_once "vendor/autoload.php";

$fat = \Base::instance();

$fat->config('config.ini');
$fat->config('../fatfree.my.cnf');

$fat->set('system.git', exec('git rev-parse --short HEAD'));

/* Commons */
$fat->route('GET /commons', 'route\commons->index');
$fat->route('GET /commons/category', 'route\commons->search');
$fat->route('GET /commons/category/@category/@user', 'route\commons->search');
$fat->route('GET /commons/category/@category', 'route\commons->search');
$fat->route('POST /commons/category', 'route\commons->category_redirect');

/* Internet Archive */
$fat->route('GET /internet-archive', 'route\archive->index');
$fat->route('POST /internet-archive/process', 'route\archive->process');

/* ORES */
$fat->route('GET /ores', 'route\ores->index');
$fat->route('POST /ores/process', 'route\ores->process');

\helper\formaters::registry();

$fat->run();
