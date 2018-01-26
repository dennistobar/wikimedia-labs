<?php

namespace route;

use \model\commons\search;

class commons extends main
{
    public function index(\Base $fat)
    {
        $fat->set('page.contents', 'commons/index.html');
    }

    public function search(\Base $fat)
    {
        $options = ['cat' => $fat->get('PARAMS.category'), 'user' => $fat->get('PARAMS.user')];
        if (is_null($options['user'])) {
            $fat->set('page.contents', 'commons/users.html');
            $fat->set('rows', search::resume($options));
        } else {
            $fat->set('page.contents', 'commons/details_users.html');
            $fat->set('rows', search::searching($options));
        }
        $fat->set('category', ['name' => $options['cat'], 'search' => $options['cat'], 'user' => $options['user']]);
    }

    public function category_redirect(\Base $fat)
    {
        $cat = str_replace(" ", "_", $fat->get('POST.category'));
        $base = sprintf("%s://%s%s", $fat->get('SCHEME'), $fat->get('HOST'), $fat->get('BASE'));
        $fat->reroute($base.'/commons/category/'.$cat);
        return $fat;
    }

    public function beforeroute(\Base $fat)
    {
        $fat->set('page.title', 'Commons Tools');
    }
}
