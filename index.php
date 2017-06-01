<?php

require_once "vendor/autoload.php";

$f3 = \Base::instance();

$f3->config('config.ini');
$f3->config('../fatfree.my.cnf');

$f3->route('GET /commons', 'controller\commons::index');
$f3->route('POST|GET /commons/users', 'controller\commons::users_category');
$f3->route('GET /commons/users/@cat', 'controller\commons::users_category');
$f3->route('GET /commons/users/@cat/@user', 'controller\commons::users_category_detail');
$f3->route('GET /wlm', 'controller\wlm::index');
$f3->route('GET /wlm/@view', 'controller\wlm::@view');
$f3->route('GET /stats', 'controller\stats::index');
$f3->route('POST /stats/process', 'controller\stats::process');

$f3->run();
