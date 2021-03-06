<?php

namespace model;

class database extends \DB\SQL
{
    protected static $instance = [];

    /**
     * Constructor de clase
     */
    public function __construct(\Base $f3, string $server = 'commonswiki', string $database = 'commonswiki')
    {
        $database = stripos($database, 'wiki') !== false ? $database . '_p' : $database;
        $dns = sprintf('mysql:host=%s.labsdb;dbname=%s', $server, $database);
        parent::__construct($dns, $f3->get('db.user'), $f3->get('db.password'));
    }

    /**
     * Obtiene la instancia a partir del servidor y base de datos
     */
    public static function instance(string $server, string $database): \DB\SQL
    {
        if (is_null(self::$instance[md5($server . $database)])) {
            $database = stripos($database, 'wiki') !== false ? $database . '_p' : $database;
            $port = self::setMyPort($server);
            $dns = sprintf('mysql:host=%s.labsdb:%d;dbname=%s', $server, $port, $database);
            self::$instance[md5($server . $database)] = new \DB\SQL($dns, \F3::get('db.user'), \F3::get('db.password'));
        }
        return self::$instance[md5($server . $database)];
    }

    /* Ok, terrible hack, but one connection per port in develop is so... 8) */
    public static function setMyPort($server)
    {
        if (stripos(\F3::get('HOST'), 'wmflabs.org') !== false || stripos(\F3::get('HOST'), 'toolforge') !== false) {
            return 3306;
        }

        return $server === 'tools' ? 4712 : 4711;
    }
}
