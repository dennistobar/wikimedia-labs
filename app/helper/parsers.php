<?php

namespace helper;

class parsers
{
    public static function timestamp($string)
    {
        return [
            'year' => substr($string, 0, 4),
            'month' => substr($string, 4, 2),
            'day' => substr($string, 6, 2),
            'hour' => substr($string, 8, 2),
            'minute' => substr($string, 10, 2),
            'sec' => substr($string, 12, 2)
    ];
    }
}
