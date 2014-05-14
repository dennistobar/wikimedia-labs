<?php

$f3=require('lib/base.php');

$f3->config('config.ini');
$f3->config('../fatfree.my.cnf');

$f3->route('GET /', function($f3){
    echo $f3->get('BASE');
});

$f3->route('GET /?commons/users/category', '\controller\commons->users_category');
$f3->route('GET /?commons/users/category/@cat', '\controller\commons->users_category');

$f3->run();
