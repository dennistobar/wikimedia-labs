<?php

$f3=require('lib/base.php');


$f3->config('config.ini');
$f3->config('../fatfree.my.cnf');

$f3->route('GET /superzerocool', function($f3){
    echo $f3->get('BASE');
});

$f3->route('GET /', function($f3){
    echo $f3->get('BASE');
});

$f3->route('GET /test', function(){
    echo 'a';
});

$f3->route('GET /superzerocool/commons', 'controller\commons::index');
$f3->route('GET /superzerocool/commons/users/@cat', 'controller\commons::users_category');

$f3->run();

