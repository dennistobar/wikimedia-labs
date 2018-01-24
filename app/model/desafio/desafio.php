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

    public function totals()
    {
        return ediciones::totals($this->desafio_name);
    }

    public static function instance()
    {
        return new self;
    }

    public function has_resumen()
    {
        return !!$this->desafio_has_resumen;
    }
}
