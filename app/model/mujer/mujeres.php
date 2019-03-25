<?php

namespace model\mujer;

class mujeres extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(\model\database::instance('tools', \F3::get('db.user') . '__desafio'), 'mujeres');
    }
}
