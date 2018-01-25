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

        $qDetails = '
            select ifnull(oi_user_text, img_user_text) as user, img_name, img_size, img_metadata, img_timestamp, img_width, img_height,
                (select count(distinct oi_timestamp) from oldimage where oi_name = img_name) as revs
            from page, image
            left join oldimage o1
                on oi_name = img_name
                and oi_timestamp = (select min(o2.oi_timestamp) from oldimage o2 where o1.oi_name = o2.oi_name)
            , categorylinks
            where page_title = img_name
                and cl_from = page_id
                and cl_to = :cat
                and page_namespace = 6
                and ifnull(oi_user_text , img_user_text) = ifnull(:user, ifnull(oi_user_text , img_user_text))
                order by img_timestamp DESC;';
        return \model\database::instance('commonswiki', 'commonswiki')->exec($qDetails, $options, 50);
    }

    public static function resume($options = [])
    {
        $result = self::search($options);
        $users = array_combine(array_column($result, 'user'), array_fill(0, count($result), 0));
        array_walk($users, function (&$el, $index) use ($result) {
            $el = count(array_intersect(array_column($result, 'user'), [$index]));
        });
        arsort($users);
        return $users;
    }
}
