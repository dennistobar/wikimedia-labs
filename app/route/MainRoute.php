<?php

namespace route;

class MainRoute
{
    public function afterroute(\Base $fat)
    {
        if ($fat->get('AJAX') === false) {
            echo \Template::instance()->render('layout.html');
        }
    }
}
