<?php

namespace model\commons;

use \model\commons\transformation;

class search
{
    public static function searching($options = [])
    {
        $options = array_merge(['cat' => null, 'user' => null], $options);
        $options['cat'] = str_replace(' ', '_', $options['cat']);
        $options = self::parameters($options, ['cat', 'user']);
        $data = searchBase::select([], $options);
        return transformation\transform::create($data, [transformation\urlCommons::create(), transformation\url::create(), transformation\size::create()]);
    }

    public static function resume($options = [])
    {
        $options = array_merge(['cat' => null, 'user' => null], $options);
        $options['cat'] = str_replace(' ', '_', $options['cat']);
        $options = self::parameters($options, ['cat', 'user']);
        $result = searchBase::select(['user', 'img_name'], $options);
        $users = array_combine(array_column($result, 'user'), array_fill(0, count($result), 0));
        array_walk($users, function (&$el, $index) use ($result) {
            $el = count(array_intersect(array_column($result, 'user'), [$index]));
        });
        arsort($users);
        return $users;
    }

    private static function parameters($options, $valid = [])
    {
        return array_filter($options, function ($f) use ($valid) {
            return in_array($f, $valid);
        }, ARRAY_FILTER_USE_KEY);
    }
}
