<?php

namespace model\commons;

class search
{
    public static function search($options = [])
    {
        $options = array_merge(['cat' => null, 'user' => null], $options);
        $options['cat'] = str_replace(' ', '_', $options['cat']);

        $options = array_filter($options, function ($f) {
            return in_array($f, ['cat', 'user']);
        }, ARRAY_FILTER_USE_KEY);

        return searchBase::select([], $options);
    }

    public static function resume($options = [])
    {
        $options = array_merge(['cat' => null, 'user' => null], $options);
        $options['cat'] = str_replace(' ', '_', $options['cat']);

        $options = array_filter($options, function ($f) {
            return in_array($f, ['cat', 'user']);
        }, ARRAY_FILTER_USE_KEY);

        $result = searchBase::select(['user', 'img_name'], $options);
        $users = array_combine(array_column($result, 'user'), array_fill(0, count($result), 0));
        array_walk($users, function (&$el, $index) use ($result) {
            $el = count(array_intersect(array_column($result, 'user'), [$index]));
        });
        arsort($users);
        return $users;
    }
}
