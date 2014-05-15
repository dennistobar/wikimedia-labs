<?php

namespace controller;

class commons {

    public static function index($f3){
        $f3->set('title', 'Commons Tools');
        $f3->set('output', 'commons/index.html');
        echo \Template::instance()->render('layout.html');
    }

    public static function users_category($f3){
        $cat = null;
        $user = null;
        if ($f3->exists('PARAMS.cat') === true){
            $cat = $f3->get('PARAMS.cat');
        }
        elseif($f3->get('VERB') === 'POST'){
            $f3->reroute('/superzerocool/commons/'.$f3->get('POST.category'));
        }

        $cat_search = str_replace(" ", "_", $cat);

        $db = new \helper\database($f3, 'commonswiki', 'commonswiki');
        $res = $db->exec('select img_user_text user, count(1) number from page, image, categorylinks where page_title = img_name and cl_from = page_id and cl_to = ? and page_namespace = 6 group by 1 order by 2 DESC', $cat_search);

        $f3->set('category', $cat);
        $f3->set('category_search', $cat_search);
        $f3->set('rows', $res);

        $f3->set('title', 'Commons Tools');
        $f3->set('output', 'commons/users.html');
        echo \Template::instance()->render('layout.html');
    }

}