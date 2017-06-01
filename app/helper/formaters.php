<?php

namespace helper;

class formaters extends \Prefab
{
    public function url($string)
    {
        return rawurlencode($string);
    }

    public static function registry()
    {
        \Template::instance()->filter('url', '\helper\formaters::instance()->url');
    }
}
