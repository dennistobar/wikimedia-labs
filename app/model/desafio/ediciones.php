<?php

namespace model\desafio;

class ediciones extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(\model\database::instance('tools', \F3::get('db.user').'__desafio'), 'ediciones');
    }

    public static function user($user)
    {
        return (new self)->find(['rc_user_text = ?', $user]);
    }
}
