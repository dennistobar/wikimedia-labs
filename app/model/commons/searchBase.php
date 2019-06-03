<?php

namespace model\commons;

class searchBase
{
    private static $data = [
        'user' => 'user_name',
        'revs' => '(select count(distinct oi_timestamp) from oldimage where oi_name = img_name)',
        'img_name', 'img_size', 'img_timestamp', 'img_width', 'img_height',
        'user_registration',
    ];

    public static function select($params = [], $options = [])
    {
        $select = [];
        $params = $params ?: self::$data;
        foreach ($params as $key => $param) {
            if (array_key_exists($param, self::$data)) {
                $select[] = self::$data[$param] . ' as ' . $param;
            } elseif (!is_numeric($key) && array_key_exists($key, self::$data)) {
                $select[] = self::$data[$key] . ' as ' . $key;
            } else {
                $select[] = $param;
            }
        }

        $qDetails = '
                SELECT ' . implode(',', $select) . '
        from page
        INNER JOIN image ON page_title = img_name
        INNER JOIN categorylinks ON cl_from = page_id
        LEFT JOIN oldimage o1 ON oi_name = img_name
            AND oi_timestamp = (select min(o2.oi_timestamp) from oldimage o2 where o1.oi_name = o2.oi_name)
        LEFT JOIN actor ON actor_id = ifnull(o1.oi_actor, img_actor)
        LEFT JOIN user ON user_id = actor_user
        WHERE page_namespace = 6
            AND cl_to = :cat
            AND user_name = ifnull(:user, user_name)
        ORDER BY img_timestamp DESC';
        return \model\database::instance('commonswiki', 'commonswiki')->exec($qDetails, $options, 50);
    }
}
