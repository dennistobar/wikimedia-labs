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

    public function timestamp($string)
    {
        $timestamp = parsers::timestamp($string);
        return implode("-", [$timestamp['day'], $timestamp['month'], $timestamp['year']]).' '
            .implode(":", [$timestamp['hour'], $timestamp['minute']]);
    }

    public function number($string)
    {
        return number_format($string, 0, ',', ' ');
    }

    public static function registry()
    {
        \Template::instance()->filter('url', '\helper\formaters::instance()->url');
        \Template::instance()->filter('nounderline', '\helper\formaters::instance()->nounderline');
        \Template::instance()->filter('timestamp', '\helper\formaters::instance()->timestamp');
        \Template::instance()->filter('number', '\helper\formaters::instance()->number');
    }
}
