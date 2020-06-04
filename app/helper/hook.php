<?php

namespace helper;

class hook
{
    private static $hooks = [];

    public static function add($hook, $function, $args = [])
    {
        self::$hooks[$hook][] = [$function, $args];
    }

    public static function run($hook)
    {
        if (!isset(self::$hooks[$hook])) {
            return;
        }
        foreach (self::$hooks[$hook] as $el) {
            call_user_func($el[0], $el[1]);
        }
    }
}
