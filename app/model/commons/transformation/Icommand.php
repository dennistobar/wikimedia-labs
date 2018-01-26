<?php

namespace model\commons\transformation;

abstract class Icommand
{
    abstract public function execute($array);

    public static function create()
    {
        return new static();
    }
}
