<?php

require_once "vendor/autoload.php";

$fat = \Base::instance();

$fat->config('config.ini');
$fat->config('../fatfree.my.cnf');

$fat->set('system.git', exec('git rev-parse --short HEAD'));

/* Commons */
$fat->route('GET /commons', 'route\commons->index');
$fat->route('POST /commons/category', 'route\commons->category_redirect');
$fat->route('GET /commons/category/@category', 'route\commons->category_search');
$fat->route('GET /commons/category/@category/@user', 'route\commons->category_user_search');

/* Wiki Loves X */
$fat->route('GET /stats', 'route\stats->index');
$fat->route('POST /stats/process', 'route\stats->process');

/* Desafio de edicion */
$fat->route('GET /desafio', 'route\desafio->index');
$fat->route('GET /desafio/@user', 'route\desafio->user');
$fat->route('GET /desafio/day/@day', 'route\desafio->day');
/** Cron **/
$cron = \Cron::instance();
$cron->set('jobDesafio', 'route\desafio->cronSQL', '*/5 * * * *');
$cron->web = true;


\helper\formaters::registry();

$fat->run();
