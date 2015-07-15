<?php

namespace model;

class stats{


    private $category;

    public function __construct($category){
        $this->category = $category;
    }

    public function getTotals(){
        $db = new \helper\database(\F3::instance(), 'commonswiki', 'commonswiki');
        $res = $db->exec("
            select count(distinct img_user) users, count(1) images
            from categorylinks 
                join page on page_id = cl_from 
                join image on img_name = page_title 
            where cl_to = :cat
                and cl_type = 'file'", ['cat' => $this->category]);
        return array_pop($res);
    }

    public function getUsers($initial, $final){
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
order by 4 desc", ['cat' => $this->category, 'initial' => $initial, 'final' => $final]);
        return $res;
    }


}