<?php

namespace model;

class stats{


    private $params, $cost;


    public function __construct($category, $initial, $final, $cost){
        $this->params = ['cat' => $category, 'ini' => $initial, 'fin' => $final];
        $this->cost = $cost;
    }

    public function getTotals(){
        $data = $this->getUsers();
        $tmp = [
            'users' => count(array_column($data, 'user_name')),
            'uploads' => array_sum(array_column($data, 'uploads')),
            ];

    }

    public function getUsers(){
        $db = new \helper\database(\F3::instance(), 'commonswiki', 'commonswiki');
        $res = $db->exec("
            select user_name, user_registration, 
    (case when user_registration between :initial and :final then 1 else 0 end) newbie,
    count(1) uploads
from categorylinks 
join page pg on page_id = cl_from 
join image img on img_name = page_title 
join `user` us on img_user = user_id
where cl_to = :cat
and cl_type = 'file'
group by 1, 2, 3
order by 4 desc", $this->params);
        return $res;
    }


}