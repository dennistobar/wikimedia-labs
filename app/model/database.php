<?php

namespace model;

class database extends \DB\SQL
{
    protected static $instance = [];

    public function __construct($f3, $server = 'commonswiki', $database = 'commonswiki')
    {
        $dns = sprintf('mysql:host=%s.labsdb;dbname=%s_p', $server, $database);
        parent::__construct($dns, $f3->get('db.user'), $f3->get('db.password'));
    }

    public static function instance($server, $database)
    {
        if (is_null(self::$instance[md5($server.$database)])) {
            $dns = sprintf('mysql:host=%s.labsdb;dbname=%s_p', $server, $database);
            self::$instance[md5($server.$database)] = new \DB\SQL($dns, \F3::get('db.user'), \F3::get('db.password'));
        }
        return self::$instance[md5($server.$database)];
    }
}
