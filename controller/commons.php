<?php

namespace controller;

class commons {
    
    public static function users_category($f3){
        $db = new \helper\database($f3, 'commonswiki', 'commons');
        var_dump($db->exec('SELECT * FROM user LIMIT 1'));
    }

}