<?php

$f3=require('lib/base.php');


$f3->config('config.ini');
$f3->config('../fatfree.my.cnf');
$f3->config('../local.ini');

$f3->route('GET /superzerocool/commons', 'controller\commons::index');
$f3->route('POST|GET /superzerocool/commons/users', 'controller\commons::users_category');
$f3->route('GET /superzerocool/commons/users/@cat', 'controller\commons::users_category');
$f3->route('GET /superzerocool/commons/users/@cat/@user', 'controller\commons::users_category_detail');
$f3->route('GET /superzerocool/wlm', 'controller\wlm::index');
$f3->route('GET /superzerocool/wlm/@view', 'controller\wlm::@view');
$f3->route('GET /superzerocool/stats', 'controller\stats::index');
$f3->route('POST /superzerocool/stats/process', 'controller\stats::process');

$f3->run();

