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
            'sec' => substr($string, 12, 2),
        ];
    }

    public static function mes($mes)
    {
        $meses = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];
        return $meses[$mes];
    }

    public static function urlCommons($file, $size = 300)
    {
        $file = formaters::instance()->putunderline($file);
        $url = 'https://upload.wikimedia.org/wikipedia/commons/thumb/%s/%s/%s/%spx-%s';
        $md5 = md5($file);

        return sprintf($url, substr($md5, 0, 1), substr($md5, 0, 2), urlencode($file), $size, urlencode($file));
    }
}
