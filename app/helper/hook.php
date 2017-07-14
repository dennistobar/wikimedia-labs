<?php

namespace helper;

class hook
{
    private static $hooks = [];

    public static function add($hook, $fn, $args = [])
    {
        self::$hooks[$hook][] = [$fn, $args];
    }

    public static function run($hook)
    {
        if (!isset(self::$hooks[$hook])) {
            return ;
        }
        foreach (self::$hooks[$hook] as $el) {
            call_user_func($el[0], $el[1]);
        }
        return ;
    }
}
