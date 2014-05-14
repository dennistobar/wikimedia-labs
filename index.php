<?php

$f3=require('lib/base.php');


$f3->config('config.ini');
$f3->config('../fatfree.my.cnf');
$f3->config('../local.ini');

$f3->route('GET /superzerocool/commons', 'controller\commons::index');
$f3->route('POST|GET /superzerocool/commons/users', 'controller\commons::users_category');
$f3->route('GET /superzerocool/commons/users/@cat', 'controller\commons::users_category');

$f3->run();

