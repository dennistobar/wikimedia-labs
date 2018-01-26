<?php

namespace model\commons\transformation;

class resume extends Icommand
{
    public function execute($array)
    {
        $users = array_fill_keys(array_column($array, 'user'), 0);
        array_walk($users, function (&$el, $index) use ($array) {
            $el = count(array_intersect(array_column($array, 'user'), [$index]));
        });
        arsort($users);
        return $users;
    }
}
