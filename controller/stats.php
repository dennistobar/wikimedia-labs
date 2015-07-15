<?php

namespace controller;

class stats {

    public static function index($f3){
        $f3->set('title', 'Wiki Loves (Whatever)');
        $f3->set('output', 'stats/index.html');
        echo \Template::instance()->render('layout.html');
    }

    public static function process($f3){

        $category = str_replace(" ", "-", $f3->get('POST.category'))
        $stats = new \model\stats($category);

        $f3->set('totals', $stats->getTotals());
        $f3->set('users', $stats->getUsers($f3->get('POST.initial'), $f3->get('POST.final')));

        $f3->set('title', 'Wiki Loves (Whatever)');
        $f3->set('output', 'stats/dashboard.html');
        echo \helper\ExtTemplate::instance()->render('layout.html');
    }

    public static function users_category_detail($f3){
        $cat = null;
        $user = null;
        if ($f3->exists('PARAMS.cat') === true){
            $cat = $f3->get('PARAMS.cat');
        }
        if ($f3->exists('PARAMS.user') === true){
            $user = $f3->get('PARAMS.user');
        }
        if($f3->get('VERB') === 'POST'){
            $f3->reroute('/superzerocool/commons/users/'.$f3->get('POST.category'));
        }

        $cat_search = str_replace(" ", "_", $cat);

        $db = new \helper\database($f3, 'commonswiki', 'commonswiki');
        $res = $db->exec('
            select img_name, img_size, img_metadata, img_timestamp, img_width, img_height, 
                (select count(distinct oi_timestamp) from oldimage where oi_name = img_name) as revs
            from page, image 
            left join oldimage o1
                on oi_name = img_name
                and oi_timestamp = (select min(o2.oi_timestamp) from oldimage o2 where o1.oi_name = o2.oi_name)
            , categorylinks 
            where page_title = img_name 
                and cl_from = page_id 
                and cl_to = ?
                and page_namespace = 6 
                and ifnull(oi_user_text , img_user_text) = ?
                order by img_timestamp DESC;'
                , array(1=>$cat_search, 2=>$user));

        $f3->set('category', $cat);
        $f3->set('category_search', $cat_search);
        $f3->set('user', $user);
        $f3->set('rows', $res);

        $f3->set('title', 'Commons Tools');
        $f3->set('output', 'commons/details_users.html');
        echo \helper\ExtTemplate::instance()->render('layout.html');
    }

}
