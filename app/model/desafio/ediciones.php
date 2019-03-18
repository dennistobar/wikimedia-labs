<?php

namespace model\desafio;

class ediciones extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(\model\database::instance('tools', \F3::get('db.user') . '__desafio'), 'ediciones');
    }

    public static function instance()
    {
        return (new self);
    }

    public static function totals($desafio)
    {
        $Desafio = self::instance();
        $Desafio->count_users = 'count(distinct rc_user_text)';
        $Desafio->count_editions = 'count(1)';
        $Desafio->count_articles = 'count(distinct rc_title)';
        $Desafio->sum_size = 'sum(rc_size_abs)';
        $Desafio->load(['desafio = ?', $desafio]);

        $elements = ['users' => 0, 'editions' => 0, 'articles' => 0, 'size' => 0];
        $elements['users'] = $Desafio->count_users;
        $elements['editions'] = $Desafio->count_editions;
        $elements['articles'] = $Desafio->count_articles;
        $elements['size'] = $Desafio->sum_size;
        return $elements;
    }

    public static function user($desafio, $user)
    {
        return self::instance()->find(['rc_user_text = ? and desafio = ?', $user, $desafio]);
    }

    public static function user_stats($desafio, $user)
    {
        $edits = self::user($desafio, $user);
        $bytes = array_sum(array_map(function ($edit) {
            return (int) $edit['rc_size_abs'];
        }, $edits));
        $articles = count(array_unique(array_map(function ($edit) {
            return $edit['rc_title'];
        }, $edits)));
        return ['size' => $bytes, 'articles' => $articles, 'edits' => count($edits)];
    }

    public static function day($desafio, $day, $user = null)
    {
        return self::instance()->find(['substring(rc_timestamp, 1, 8) = ? and rc_user_text = ifnull(?, rc_user_text) and desafio = ?', $day, $user, $desafio]);
    }
}
