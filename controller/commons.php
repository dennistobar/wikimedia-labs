<?php

namespace controller;

class commons {
    
    

    public static function users_category($f3){
    	$db = new \DB\SQL('mysql:host=commonswiki.labsdb', $f3->get('user'), $f3->get('password'));
        var_dump($db->exec('SELECT * FROM user LIMIT 1'));
    }

}