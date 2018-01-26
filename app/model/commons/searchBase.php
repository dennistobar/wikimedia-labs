<?php

namespace model\commons;

class searchBase
{
    private static $data = [
        'user' => 'ifnull(oi_user_text, img_user_text)',
        'revs' => '(select count(distinct oi_timestamp) from oldimage where oi_name = img_name)',
        'img_name', 'img_size', 'img_timestamp', 'img_width', 'img_height',
        'user_registration'
    ];

    public static function select($params = [], $options = [])
    {
        $select = [];
        $params = $params ?: self::$data;
        foreach ($params as $key => $param) {
            if (array_key_exists($param, self::$data)) {
                $select[] = self::$data[$param].' as '.$param;
            } elseif (!is_numeric($key) && array_key_exists($key, self::$data)) {
                $select[] = self::$data[$key].' as '.$key;
            } else {
                $select[] = $param;
            }
        }

        $qDetails = '
            select '.implode(',', $select).'
            from page, image
            left join oldimage o1
                on oi_name = img_name
                and oi_timestamp = (select min(o2.oi_timestamp) from oldimage o2 where o1.oi_name = o2.oi_name)
            left join `user`
                on user_id = ifnull(oi_user, img_user)
            , categorylinks
            where page_title = img_name
                and cl_from = page_id
                and cl_to = :cat
                and page_namespace = 6
                and ifnull(oi_user_text , img_user_text) = ifnull(:user, ifnull(oi_user_text , img_user_text))
                order by img_timestamp DESC';
        return \model\database::instance('commonswiki', 'commonswiki')->exec($qDetails, $options, 50);
    }
}
