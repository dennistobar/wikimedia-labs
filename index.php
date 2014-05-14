<?php

$f3=require('lib/base.php');

$f3->route('GET /', function($f3){
    echo $f3->get('BASE');
});

$f3->route('GET /test', function($f3){
    echo 'test '.$f3->get('BASE');
});

$f3->run();
