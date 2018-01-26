<?php

namespace model\commons\transformation;

class url extends Icommand
{
    public function execute($array)
    {
        array_walk($array, function (&$f) {
            $f['url'] = urlencode($f['img_name']);;
        });
        return $array;
    }
}
