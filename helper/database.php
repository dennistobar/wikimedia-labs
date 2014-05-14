<?php

namespace helper;

class database extends \DB\SQL{
    
    public function __construct($f3, $server = 'commonswiki', $database = 'commonswiki'){
        $dns = sprintf('mysql:host=%s.labsdb;dbname=%s_p', $server, $database);
        parent::__construct($dns, $f3->get('user'), $f3->get('password'));
    }

}