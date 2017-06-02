<?php

namespace helper;

class formaters extends \Prefab
{
    public function url($string)
    {
        return rawurlencode($string);
    }

    public function nounderline($string)
    {
        return str_replace("_", " ", $string);
    }

    public static function registry()
    {
        \Template::instance()->filter('url', '\helper\formaters::instance()->url');
        \Template::instance()->filter('nounderline', '\helper\formaters::instance()->nounderline');
    }
}
