<?php

namespace model\commons\transformation;

class urlCommons extends Icommand
{
    public function execute($array)
    {
        array_walk($array, function (&$f) {
            $f['commons'] = \helper\parsers::urlCommons($f['img_name'], min([300, $f['img_width']]));
        });
        return $array;
    }
}
