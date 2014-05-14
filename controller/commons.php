<?php

namespace controller;

class commons {
    
    private $db = new \DB\SQL('mysql:host=commonswiki.labsdb', $f3->get('user'), $f3->get('password'));

    public function users_category(){
        var_dump($this->db->exec('SELECT 1 FROM user'));
    }

}