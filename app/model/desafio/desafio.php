<?php

namespace model\desafio;

class desafio extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(\model\database::instance('tools', \F3::get('db.user').'__desafio'), 'desafio');
    }

    public static function getByName($name)
    {
        return (new self)->find(['desafio_name = ?', $name]);
    }
}
