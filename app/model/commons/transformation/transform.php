<?php

namespace model\commons\transformation;

class transform
{
    private $transform = [];
    private $array = [];

    public function __construct($array)
    {
        $this->array = $array;
    }

    public function addTransform(Icommand $Command)
    {
        $this->transform[] = $Command;
    }

    public function execute()
    {
        foreach ($this->transform as $Command) {
            $this->array = $Command->execute($this->array);
        }
        return $this->array;
    }

    public static function create($array, $Commands = [])
    {
        $Self = new self($array);
        foreach ($Commands as $Command) {
            if ($Command instanceof Icommand) {
                $Self->addTransform($Command);
            }
        }
        return $Self->execute();
    }
}
