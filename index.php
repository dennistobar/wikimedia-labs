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

/* Wiki Loves X */
$fat->route('GET /stats', 'route\stats->index');
$fat->route('POST /stats/process', 'route\stats->process');

/* Desafio de edicion */
$fat->route('GET /desafio', 'route\desafio->index');
$fat->route('GET /desafio/@name', 'route\desafio->home');
$fat->route('GET /desafio/@name/@user', 'route\desafio->user');
$fat->route('GET /desafio/@name/day/@day', 'route\desafio->day');
$fat->route('GET /desafio/@name/resumen', 'route\desafio->resume');

/** Cron **/
$cron = \Cron::instance();
$cron->set('jobDesafio', 'route\cron::cronSQL', '*/5 * * * *');
$cron->set('jobCategories', 'route\cron::cronCategories', '* */6 * * *');
$cron->web = true;

/* Internet Archive */
$fat->route('GET /internet-archive', 'route\archive->index');
$fat->route('POST /internet-archive/process', 'route\archive->process');

\helper\formaters::registry();

$fat->run();
