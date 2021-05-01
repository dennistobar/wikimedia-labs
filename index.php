<?php

require_once "vendor/autoload.php";

$fat = \Base::instance();

$fat->config('config.ini');
$fat->config('../fatfree.my.cnf');

$fat->set('system.git', exec('git rev-parse --short HEAD'));

/* Commons */
$fat->route('GET /commons', 'route\CommonsRoute->index');
$fat->route('GET /commons/category', 'route\CommonsRoute->search');
$fat->route('GET /commons/category/@category/@user', 'route\CommonsRoute->search');
$fat->route('GET /commons/category/@category', 'route\CommonsRoute->search');
$fat->route('POST /commons/category', 'route\CommonsRoute->categoryRedirect');

/* Internet Archive */
$fat->route('GET /internet-archive', 'route\ArchiveRoute->index');
$fat->route('POST /internet-archive/process', 'route\ArchiveRoute->process');

/* ORES */
$fat->route('GET /ores', 'route\OresRoute->index');
$fat->route('POST /ores/process', 'route\OresRoute->process');
$fat->route('GET /ores/revid/@revid', 'route\OresRoute->getFromRevId');

\helper\formaters::registry();

$fat->run();
