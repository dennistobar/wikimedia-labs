<?php

namespace route;

class commons
{
    public function index(\Base $fat)
    {
        $fat->set('title', 'Commons Tools');
        $fat->set('output', 'commons/index.html');
        echo \Template::instance()->render('layout.html');
    }
    public function users_category($fat)
    {
        $cat = null;
        $user = null;
        if ($fat->exists('PARAMS.cat') === true) {
            $cat = $fat->get('PARAMS.cat');
        }
        if ($fat->exists('PARAMS.user') === true) {
            $user = $fat->get('PARAMS.user');
        }
        if ($fat->get('VERB') === 'POST') {
            $fat->reroute('/commons/users/'.$fat->get('POST.category'));
        }

        $cat_search = str_replace(" ", "_", $cat);

        $db = new \model\database($fat, 'commonswiki', 'commonswiki');
        $res = $db->exec('
            select ifnull(oi_user_text, img_user_text) as user, count(distinct img_name) as number
            from page, image
            left join oldimage o1
                on oi_name = img_name
                and oi_timestamp = (select min(o2.oi_timestamp) from oldimage o2 where o1.oi_name = o2.oi_name)
            , categorylinks
            where page_title = img_name
                and cl_from = page_id
                and cl_to = ?
                and page_namespace = 6
            group by 1
            order by 2 DESC', $cat_search);

        $fat->set('category', $cat);
        $fat->set('category_search', $cat_search);
        $fat->set('rows', $res);

        $fat->set('title', 'Commons Tools');
        $fat->set('output', 'commons/users.html');
        echo \Template::instance()->render('layout.html');
    }

    public function users_category_detail($fat)
    {
        $cat = null;
        $user = null;
        if ($fat->exists('PARAMS.cat') === true) {
            $cat = $fat->get('PARAMS.cat');
        }
        if ($fat->exists('PARAMS.user') === true) {
            $user = $fat->get('PARAMS.user');
        }
        if ($fat->get('VERB') === 'POST') {
            $fat->reroute('/commons/users/'.$fat->get('POST.category'));
        }

        $cat_search = str_replace(" ", "_", $cat);

        $db = new \model\database($fat, 'commonswiki', 'commonswiki');
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
                order by img_timestamp DESC;', array(1=>$cat_search, 2=>$user));

        $fat->set('category', $cat);
        $fat->set('category_search', $cat_search);
        $fat->set('user', $user);
        $fat->set('rows', $res);

        $fat->set('title', 'Commons Tools');
        $fat->set('output', 'commons/details_users.html');
        echo \Template::instance()->render('layout.html');
    }
}
