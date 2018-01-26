<?php

namespace model\commons;

use \model\commons\transformation;

class search
{
    public static function searching($options = [])
    {
        $options = self::parameters($options, ['cat', 'user']);
        $options['cat'] = str_replace(' ', '_', $options['cat']);
        $data = searchBase::select([], $options);
        return transformation\transform::create($data, [transformation\urlCommons::create(), transformation\url::create(), transformation\size::create()]);
    }

    public static function resume($options = [])
    {
        $options = self::parameters($options, ['cat', 'user']);
        $options['cat'] = str_replace(' ', '_', $options['cat']);
        $data = searchBase::select(['user', 'img_name'], $options);
        return transformation\transform::create($data, [transformation\resume::create()]);
    }

    private static function parameters($options, $valid = [])
    {
        $options = array_merge(array_fill_keys($valid, null), $options);
        return array_filter($options, function ($f) use ($valid) {
            return in_array($f, $valid);
        }, ARRAY_FILTER_USE_KEY);
    }
}
