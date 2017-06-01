<?php

namespace model;

class stats
{
    private $params;
    private $cost;


    public function __construct($category, $initial, $final, $cost)
    {
        $this->params = ['cat' => $category, 'ini' => $initial, 'fin' => $final];
        $this->cost = $cost;
    }

    public function getTotals()
    {
        $data = $this->getUsers();
        $tmp = [
        'users' => count(array_column($data, 'user_name')),
        'uploads' => array_sum(array_column($data, 'uploads')),
        'newbies' => array_sum(array_column($data, 'newbie'))
        ];
        $tmp['rate_newbies'] = $tmp['newbies']/($tmp['users'] ?: 1);
        $upl_newbie = array_filter($data, function ($f) {
            return $f['newbie'] === '1';
        });
        $tmp['newbie_uploads'] = array_sum(array_column($upl_newbie, 'uploads'));
        $tmp['rate_newbies_uploads'] = $tmp['newbie_uploads']/($tmp['uploads'] ?: 1);
        $tmp['percentile_25'] = self::getPercentile(25, array_column($data, 'uploads'));
        $tmp['percentile_50'] = self::getPercentile(50, array_column($data, 'uploads'));
        $tmp['percentile_75'] = self::getPercentile(75, array_column($data, 'uploads'));
        $tmp['percentile_90'] = self::getPercentile(90, array_column($data, 'uploads'));
        $tmp['percentile_99'] = self::getPercentile(99, array_column($data, 'uploads'));
        $tmp['veterean'] = $tmp['users'] - $tmp['newbies'];
        $tmp['veterean_uploads'] = $tmp['uploads'] - $tmp['newbie_uploads'];
        return $tmp;
    }

    public function getUsers()
    {
        if (isset($this->list) === false) {
            $db = new \model\database(\F3::instance(), 'commonswiki', 'commonswiki');
            $res = $db->exec("
                select user_name, user_registration,
                (case when user_registration between :ini and :fin then 1 else 0 end) newbie,
                count(1) uploads
                from categorylinks
                join page pg on page_id = cl_from
                join image img on img_name = page_title
                join `user` us on img_user = user_id
                where cl_to = :cat
                and cl_type = 'file'
                group by 1, 2, 3
                order by 4 desc", $this->params, 3600);
            $this->list = $res;
        }
        return $this->list;
    }

    /** http://stackoverflow.com/a/24049361 **/
    public function getPercentile($percentile, $array)
    {
        sort($array);
        $index = ($percentile/100) * count($array);
        $idx = ceil($index);
        return [
            'sum' => array_sum(array_slice($array, 0, $idx > count($array) ? $idx -1 : $idx)),
            'count' => count(array_slice($array, 0, $idx > count($array) ? $idx -1 : $idx))
            ];
    }
}
