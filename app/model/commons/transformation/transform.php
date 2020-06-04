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

    public function addTransform(Icommand $command)
    {
        $this->transform[] = $command;
    }

    public function execute()
    {
        foreach ($this->transform as $command) {
            $this->array = $command->execute($this->array);
        }

        return $this->array;
    }

    public static function create($array, $commands = [])
    {
        $self = new self($array);
        foreach ($commands as $command) {
            if ($command instanceof Icommand) {
                $self->addTransform($command);
            }
        }

        return $self->execute();
    }
}
