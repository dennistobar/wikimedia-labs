<?php

namespace model\desafio;

class ediciones extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(\model\database::instance('tools', \F3::get('db.user').'__desafio'), 'ediciones');
    }

    public static function user($desafio, $user)
    {
        return (new self)->find(['rc_user_text = ? and desafio = ?', $user, $desafio]);
    }

    public static function user_stats($desafio, $user)
    {
        $edits = self::user($desafio, $user);
        $bytes = array_sum(array_map(function ($edit) {
            return (int)$edit['rc_size_abs'];
        }, $edits));
        $articles = count(array_unique(array_map(function ($edit) {
            return $edit['rc_title'];
        }, $edits)));
        return ['size' => $bytes, 'articles' => $articles, 'edits' => count($edits)];
    }

    public static function day($desafio, $day, $user = null)
    {
        return (new self)->find(['substring(rc_timestamp, 1, 8) = ? and rc_user_text = ifnull(?, rc_user_text) and desafio = ?', $day, $user, $desafio]);
    }
}
