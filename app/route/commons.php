<?php

namespace route;

class commons extends main
{
    public function index(\Base $fat)
    {
        $fat->set('page.contents', 'commons/index.html');
    }

    public function category_redirect(\Base $fat)
    {
        $cat = str_replace(" ", "_", $fat->get('POST.category'));
        $base = sprintf("%s://%s%s", $fat->get('SCHEME'), $fat->get('HOST'), $fat->get('BASE'));
        $fat->reroute($base.'/commons/category/'.$cat);
        return $fat;
    }

    public function category_search(\Base $fat)
    {
        $Category = \model\commons\category::load($fat->get('PARAMS.category'));
        $users = $Category->users();
        $fat->set('category', $Category->parameters());
        $fat->set('rows', $users);
        $fat->set('page.contents', 'commons/users.html');
    }

    public function category_user_search(\Base $fat)
    {
        $Category = \model\commons\category::load($fat->get('PARAMS.category'));
        $users = $Category->details_user($fat->get('PARAMS.user'));
        $fat->set('category', $Category->parameters());
        $fat->set('rows', $users);
        $fat->set('page.contents', 'commons/details_users.html');
    }

    public function beforeroute(\Base $fat)
    {
        $fat->set('page.title', 'Commons Tools');
    }

}
