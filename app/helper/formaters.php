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

    public function putunderline($string)
    {
        return str_replace(" ", "_", $string);
    }

    public function timestamp($string)
    {
        $timestamp = parsers::timestamp($string);
        return implode("-", [$timestamp['day'], $timestamp['month'], $timestamp['year']]) . ' '
        . implode(":", [$timestamp['hour'], $timestamp['minute']]);
    }

    public function american_timestamp($string)
    {
        $timestamp = parsers::timestamp($string);
        return implode("-", [$timestamp['year'], $timestamp['month'], $timestamp['day']]) . ' '
        . implode(":", [$timestamp['hour'], $timestamp['minute'], $timestamp['sec']]);
    }

    public function size($string)
    {
        $sizes = ['', 'K', 'M', 'G', 'T'];
        $size = (int) $string;
        for ($i = 1; ($sizeReturn = $size / pow(1024, $i + 1)) > 1; $i++);
        return round($sizeReturn * 1024, 2) . ' ' . $sizes[$i] . 'B';
    }

    public function number($string)
    {
        return number_format($string, 0, ',', ' ');
    }

    public static function registry()
    {
        \Template::instance()->filter('url', '\helper\formaters::instance()->url');
        \Template::instance()->filter('nounderline', '\helper\formaters::instance()->nounderline');
        \Template::instance()->filter('putunderline', '\helper\formaters::instance()->putunderline');
        \Template::instance()->filter('american_time', '\helper\formaters::instance()->american_timestamp');
        \Template::instance()->filter('timestamp', '\helper\formaters::instance()->timestamp');
        \Template::instance()->filter('size', '\helper\formaters::instance()->size');
        \Template::instance()->filter('number', '\helper\formaters::instance()->number');
    }
}
