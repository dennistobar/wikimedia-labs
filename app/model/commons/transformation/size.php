<?php

namespace model\commons\transformation;

class size extends Icommand
{
    public function execute($array)
    {
        array_walk($array, function (&$f) {
            $sizes = ['', 'K', 'M', 'G', 'T'];
            for ($i = 1; ($size_return = (int)$f['img_size'] / pow(1024, $i+1)) > 1; $i++);
            $f['size'] = round($size_return*1024, 2).' '.$sizes[$i].'B';
        });
        return $array;
    }
}
